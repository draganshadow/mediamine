<?php
namespace MediaMine\CoreBundle\Tunnel\XML\Parser;

class MovieParser
{
    public function parse($path) {
        $dom = new \DomDocument();
        $dom->load($path);
        $name = $dom->getElementsByTagName('LocalTitle');
        $name = ($name->length) ? $name->item(0)->nodeValue : null;
        $summary = $dom->getElementsByTagName('Description');
        $summary = ($summary->length) ? $summary->item(0)->nodeValue : null;

        $productionYear = $dom->getElementsByTagName('ProductionYear');
        $productionYear = ($productionYear->length) ? $productionYear->item(0)->nodeValue : null;
        $IMDBrating = $dom->getElementsByTagName('IMDBrating');
        $IMDBrating = ($IMDBrating->length) ? $IMDBrating->item(0)->nodeValue : null;
        $rating = $dom->getElementsByTagName('Rating');
        $rating = ($rating->length) ? $rating->item(0)->nodeValue : null;
        $originalName = $dom->getElementsByTagName('OriginalTitle');
        $originalName = ($originalName->length) ? $originalName->item(0)->nodeValue : null;
        $budget = $dom->getElementsByTagName('Budget');
        $budget = ($budget->length) ? $budget->item(0)->nodeValue : null;
        $country = $dom->getElementsByTagName('Country');
        $country = ($country->length) ? $country->item(0)->nodeValue : null;
        $runningtime = $dom->getElementsByTagName('RunningTime');
        $runningtime = ($runningtime->length) ? $runningtime->item(0)->nodeValue : null;
        $director = $dom->getElementsByTagName('Director');
        $director = ($director->length) ? $director->item(0)->nodeValue : null;
        $writers = $dom->getElementsByTagName('WritersList');
        $writers = array_filter(array_filter(explode('|', ($writers->length) ? $writers->item(0)->nodeValue : ''), 'trim'), 'strlen');
        $trailerURL = $dom->getElementsByTagName('TrailerURL');
        $trailerURL = ($trailerURL->length) ? $trailerURL->item(0)->nodeValue : null;
        $videoBitrate = $dom->getElementsByTagName('VideoBitrate');
        $videoBitrate = ($videoBitrate->length) ? $videoBitrate->item(0)->nodeValue : null;
        $videoCodecType = $dom->getElementsByTagName('VideoCodecType');
        $videoCodecType = ($videoCodecType->length) ? $videoCodecType->item(0)->nodeValue : null;
        $videoFileSize = $dom->getElementsByTagName('VideoFileSize');
        $videoFileSize = ($videoFileSize->length) ? $videoFileSize->item(0)->nodeValue : null;
        $videoHasSubtitles = $dom->getElementsByTagName('VideoHasSubtitles');
        $videoHasSubtitles = ($videoHasSubtitles->length) ? $videoHasSubtitles->item(0)->nodeValue : null;

// IT WORK
        $genres = array();
        $all_genres = $dom->getElementsByTagName('Genre');
        foreach($all_genres as $genre) {
            $genres[] = $genre->nodeValue;
        }

        $studios = array();
        $all_studios = $dom->getElementsByTagName('Studio');
        foreach($all_studios as $studio) {
            $studios[] = $studio->nodeValue;
        }

        $persons = array();
        foreach ($dom->getElementsByTagName('Person') as $tmp) {
            $person = array (
                'name' => $tmp->getElementsByTagName('Name')->item(0)->nodeValue,
                'type' => $tmp->getElementsByTagName('Type')->item(0)->nodeValue,
                'role' => $tmp->getElementsByTagName('Role')->item(0)->nodeValue,
            );
            $persons[] = $person;
        }
        return array(
            'name' => $name,
            'summary' => $summary,
            'originalName' => $originalName,
            'productionYear' => $productionYear,
            'IMDBrating' => $IMDBrating,
            'rating' => $rating,
            'budget' => $budget,
            'genres' => $genres,
            'country' => $country,
            'runningtime' => $runningtime,
            'directors' => $director,
            'writers' => $writers,
            'studios' => $studios,
            'persons' => $persons,
            'videoBitrate' => $videoBitrate,
            'videoCodecType' => $videoCodecType,
            'videoFileSize' => $videoFileSize,
            'videoHasSubtitles' => $videoHasSubtitles,
            'trailerURL' => $trailerURL,
        );
    }
}