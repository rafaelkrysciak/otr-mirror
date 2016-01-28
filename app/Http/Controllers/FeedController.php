<?php namespace App\Http\Controllers;

use App\Http\Requests;

use App\Station;
use App\TvProgramsView;
use Carbon\Carbon;

class FeedController extends Controller
{

    public function twitter($lang = 'de')
    {
        $language = Station::where('language_short', '=', $lang)->first()->language;

        $rows = TvProgramsView::with('film')
            ->where('language', '=', $language)
            ->where('tvseries', '=', false)
            ->where('imdb_votes', '>', 10000)
            ->groupBy('film_id')
            ->orderBy('start', 'desc')
            ->limit(100)
            ->get();

        $rss = new \UniversalFeedCreator();
        $rss->title = "HQ-Mirror";
        $rss->description = "Filme und Serien vom HQ-Mirror";

        //optional
        $rss->descriptionTruncSize = 500;
        $rss->descriptionHtmlSyndicated = true;

        $rss->link = url('/');

        foreach ($rows as $row) {
            $item = new \FeedItem();
            $item->title = $row->title;
            $item->link = url('/tvprogram/film/' . $row->film_id . '/' . $row->language . '/any');

            $description = $row->film->country() . '(' . $row->film->year . ') ';


            $description .= 'IMDb:' . number_format($row->imdb_rating, 1, ',', '.') . '(' . ceil($row->imdb_votes / 1000) . 'K) ';
            //$description .= 'Mit:';
            foreach ($row->film->filmStars->take(3) as $actor) {
                $description .= $actor->star . ',';
            }
            $description = trim($description, ',') . ' ';

            $genres = $row->film->genres();
            $description .= (count($genres) > 0 ? '#' . $genres[0] : '') . ' ';
            $description .= '#' . str_replace(' ', '', $row->station);

            $item->description = $description;

            $item->date = $row->start->format(Carbon::ATOM);

            $rss->addItem($item);
        }

        return response($rss->createFeed("ATOM"))
            ->header('Content-Type', 'text/xml');
    }

    public function facebook($lang = 'de')
    {
        $language = Station::where('language_short', '=', $lang)->first()->language;

        $rows = TvProgramsView::with('film')
            ->where('language', '=', $language)
            ->where('tvseries', '=', false)
            ->where('imdb_votes', '>', 10000)
            ->groupBy('film_id')
            ->orderBy('start', 'desc')
            ->limit(100)
            ->get();

        $rss = new \UniversalFeedCreator();
        $rss->title = "HQ-Mirror";
        $rss->description = "Filme und Serien vom HQ-Mirror";

        //optional
        $rss->descriptionTruncSize = 500;
        $rss->descriptionHtmlSyndicated = true;

        $rss->link = url('/');

        foreach ($rows as $row) {
            $item = new \FeedItem();
            $item->title = $row->title;
            $item->link = url('/tvprogram/film/' . $row->film_id . '/' . $row->language . '/any');

            $description = $row->film->country() . '(' . $row->film->year . ') ';


            $description .= 'IMDb: ' . number_format($row->imdb_rating, 1, ',', '.') . ' (' . ceil($row->imdb_votes / 1000) . 'K) * ';

            if ($row->film->filmStars->count() > 0) {
                $description .= 'Mit: ';
                $description .= implode(', ', $row->film->filmStars->take(4)->lists('star')).' * ';
            }

            if ($row->film->directors()) {
                $description .= 'Regie: ';
                $description .= implode(', ', $row->film->directors()).' * ';
            }

            if ($row->film->genres()) {
                $description .= 'Genre: ';
                $description .= '#'.implode(', #', $row->film->genres()).' * ';
            }

            $description .= 'Sender: #' . str_replace(' ', '', $row->station).' ';

            if ($row->film->amazon_image) {
                $description .= '<img src="'.$row->film->imageResize(250).'">';
            }

            $item->description = $description;

            $item->date = $row->start->format(Carbon::ATOM);

            $rss->addItem($item);
        }

        return response($rss->createFeed("ATOM"))
            ->header('Content-Type', 'text/xml');
    }

    public function rss($lang = 'de')
    {

        $language = Station::where('language_short', '=', $lang)->first()->language;

        $rss = new \UniversalFeedCreator();
        // $rss->useCached(); // use cached version if age < 1 hour
        $rss->title = "HQ-Mirror";
        $rss->description = "Filme und Serien vom HQ-Mirror";

        //optional
        $rss->descriptionTruncSize = 500;
        $rss->descriptionHtmlSyndicated = true;

        $rss->link = url('/');
        //$rss->syndicationURL = "http://www.dailyphp.net/" . $_SERVER["PHP_SELF"];


        /*		$image = new FeedImage();
                $image->title = "dailyphp.net logo";
                $image->url = "http://www.dailyphp.net/images/logo.gif";
                $image->link = "http://www.dailyphp.net";
                $image->description = "Feed provided by dailyphp.net. Click to visit.";

                //optional
                $image->descriptionTruncSize = 500;
                $image->descriptionHtmlSyndicated = true;

                $rss->image = $image;
        */

        $rows = TvProgramsView::where('language', '=', $language)
            ->orderBy('start', 'desc')
            ->groupBy('tv_program_id')
            ->limit(100)
            ->get();

        foreach ($rows as $row) {
            $item = new \FeedItem();
            $item->title = $row->title;
            $item->link = url('/tvprogram/show/' . $row->tv_program_id);
            $item->description = $row->description;

            //optional
            $item->descriptionTruncSize = 500;
            $item->descriptionHtmlSyndicated = true;

            $item->date = $row->start->format(Carbon::ATOM);
            //$item->source = "http://www.dailyphp.net";
            //$item->author = "John Doe";

            $rss->addItem($item);
        }

        //header('Content-Type', 'application/rss+xml');
        header('Content-Type', 'text/xml');
        echo $rss->createFeed("ATOM");
    }

}
