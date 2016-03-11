<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Promotion;
use Aws\CloudFront\Exception\Exception;
use Intervention\Image\Facades\Image;
use Request;


class PromotionController extends Controller
{


    function __construct()
    {
        $this->middleware('admin');
    }


    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $promotions = Promotion::with('tvProgram')->orderBy('position')->get();

        return view('promotion.index', compact('promotions'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('promotion.create');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $data = Request::all();
        $data['active'] = array_key_exists('active', $data);
        $promotion = Promotion::create($data);

        try {
            if (Request::hasFile('image')) {
                Image::make(Request::file('image'))
                    ->fit(945, 650)
                    ->save(public_path() . '/img/promotions/' . $promotion->id . '.jpg');
            } else {
                Image::make($data['image_url'])
                    ->fit(945, 650)
                    ->save(public_path() . '/img/promotions/' . $promotion->id . '.jpg');
            }
        } catch (Exception $e) {
            $promotion->delete();
            \Log::error($e);

            return redirect()->back()->withErrors([$e->getMessage()]);
        }

        return redirect('promotion');
    }


    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        //
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $promotion = Promotion::findOrFail($id);
        $tvprograms = [];
        if ($promotion->tvProgram) {
            $tvprograms = [
                $promotion->tv_program_id =>
                    $promotion->tvProgram->title . ' (' . $promotion->tvProgram->station . ' ' . $promotion->tvProgram->start->format('Y-m-d H:i') . ')',
            ];
        }

        return view('promotion.edit', compact('promotion', 'tvprograms'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function update($id)
    {
        $data = Request::all();
        $data['active'] = array_key_exists('active', $data);

        try {
            \DB::beginTransaction();

            $promotion = Promotion::findOrFail($id);
            $promotion->update($data);

            if (Request::hasFile('image')) {
                Image::make(Request::file('image'))
                    ->fit(945, 650)
                    ->save(public_path() . '/img/promotions/' . $promotion->id . '.jpg');
            } elseif ($data['image_url']) {
                Image::make($data['image_url'])
                    ->fit(945, 650)
                    ->save(public_path() . '/img/promotions/' . $promotion->id . '.jpg');
            }

            \DB::commit();

        } catch (Exception $e) {
            \DB::rollBack();
            \Log::error($e);

            return redirect()->back()->withErrors([$e->getMessage()]);
        }

        return redirect('promotion');

    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        Promotion::destroy($id);
        unlink(public_path() . '/img/promotions/' . $id . '.jpg');

        return redirect('promotion');
    }

}
