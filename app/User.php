<?php namespace App;

use App\Services\DownloadService;
use \Auth;
use Carbon\Carbon;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{

    use Authenticatable, CanResetPassword;

    const WATCHED = 'watched';
    const FAVORITE = 'favorite';
    const DOWNLOADED = 'downloaded';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password', 'confirmation_code', 'premium_valid_until'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];
    protected $dates = ['premium_valid_until'];


    public function addTvProgramToList(TvProgram $tvProgram, $list)
    {
        if (!in_array($list, [self::WATCHED, self::DOWNLOADED, self::FAVORITE])) {
            throw new \Exception('List ' . $list . ' is unknown');
        }

        $count = $this->tvPrograms()
            ->wherePivot('type', '=', $list)
            ->where('tv_program_id', '=', $tvProgram->id)
            ->count();

        if ($count == 0) {
            $this->tvPrograms()->attach($tvProgram, ['type' => $list]);
        }
    }


    public function removeTvProgramFromList(TvProgram $tvProgram, $list)
    {
        if (!in_array($list, [self::WATCHED, self::DOWNLOADED, self::FAVORITE])) {
            throw new \Exception('List ' . $list . ' is unknown');
        }

        $this->tvPrograms()->wherePivot('type', '=', $list)->detach($tvProgram);
    }


    public function addFavorite(TvProgram $tvProgram)
    {
        $this->tvPrograms()->attach($tvProgram, ['type' => self::FAVORITE]);
    }


    public function addWatched(TvProgram $tvProgram)
    {
        $this->tvPrograms()->attach($tvProgram, ['type' => self::WATCHED]);
    }


    public function addDownload(TvProgram $tvProgram)
    {
        $this->tvPrograms()->attach($tvProgram, ['type' => self::DOWNLOADED]);
    }


    public function getFavorites()
    {
        return $this->tvPrograms()
            ->wherePivot('type', '=', self::FAVORITE)
            ->orderBy('start', 'desc')
            ->get();
    }


    public function getWatched()
    {
        return $this->tvPrograms()
            ->wherePivot('type', '=', self::WATCHED)
            ->orderBy('start', 'desc')
            ->get();
    }


    public function getDownload()
    {
        return $this->tvPrograms()
            ->orderBy('start', 'desc')
            ->wherePivot('type', '=', self::DOWNLOADED)
            ->get();
    }


    public function countDownloadsCurrentMonth()
    {
        return $this->tvPrograms()
            ->wherePivot('type', '=', self::DOWNLOADED)
            ->wherePivot('created_at', '>', Carbon::now()->firstOfMonth())
            ->count();
    }


    public function getListsForTvPrograms($TvPrograms)
    {
        if ($TvPrograms instanceof Collection) {
            $TvPrograms = $TvPrograms->modelKeys();
        }

        $rows = $this->tvPrograms()
            ->newPivotStatement()
            ->where('user_id', $this->id)
            ->whereIn('tv_program_id', $TvPrograms)
            ->get(['type', 'tv_program_id']);

        $lists = [
            self::FAVORITE   => [],
            self::DOWNLOADED => [],
            self::WATCHED    => [],
        ];

        foreach ($rows as $row) {
            $lists[$row->type][] = $row->tv_program_id;
        }

        return $lists;
    }


    public static function getListsForTvProgram($TvProgram)
    {
        $user = Auth::user();

        if (!$user) {
            return [
                User::FAVORITE   => 'disabled',
                User::WATCHED    => 'disabled',
                User::DOWNLOADED => 'disabled',
            ];
        }


        if ($TvProgram instanceof Model) $TvProgram = $TvProgram->getKey();

        $userLists = $user->tvPrograms()
            ->newPivotStatementForId($TvProgram)
            ->lists('type');

        return [
            User::FAVORITE   => in_array(User::FAVORITE, $userLists) ? 'list-active' : '',
            User::WATCHED    => in_array(User::WATCHED, $userLists) ? 'list-active' : '',
            User::DOWNLOADED => in_array(User::DOWNLOADED, $userLists) ? 'list-active' : '',
        ];
    }


    public function tvPrograms()
    {
        return $this->belongsToMany('App\TvProgram', 'tv_program_user', 'user_id', 'tv_program_id')
            ->withPivot('type')
            ->withTimestamps();
    }


    public function paypalTransactions()
    {
        return $this->hasMany('App\PaypalTransaction')->orderBy('ordertime', 'desc');
    }


    public function extendPremium($duration, $entity = 'months')
    {
        if (empty($this->premium_valid_until) || $this->premium_valid_until->isPast()) {
            $this->premium_valid_until = Carbon::now();
        }

        if($entity == 'months') {
            $this->premium_valid_until = $this->premium_valid_until->addMonths($duration);
        } elseif($entity == 'days') {
            $this->premium_valid_until = $this->premium_valid_until->addDays($duration);
        }

        $this->save();
    }


    public function isPremium()
    {
        return $this->premium_valid_until->isFuture();
    }


    public function isAdmin()
    {
        return $this->email == 'rafael.krysciak@gmail.com';
    }


    public static function getDownloadType()
    {
        $user = Auth::user();
        if (!$user) {
            return DownloadService::GUEST;
        } else {
            return $user->isPremium() ? DownloadService::PREMIUM : DownloadService::REGISTERED;
        }
    }

    /**
     * Get films related to the user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function films()
    {
        return $this->belongsToMany('App\Film')
            ->withTimestamps();
    }

    public function hasFilm(Film $film)
    {
        return $this->films()->where('film_id','=',$film->id)->count() > 0;
    }

}
