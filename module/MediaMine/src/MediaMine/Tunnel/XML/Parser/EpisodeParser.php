<?php
namespace MediaMine\Tunnel\XML\Parser;

class EpisodeParser
{
    public function parse($path) {
        $dom = new \DomDocument();
        $dom->load($path);
        $name = $dom->getElementsByTagName('EpisodeName');
        $name = ($name->length) ? $name->item(0)->nodeValue : null;
        $summary = $dom->getElementsByTagName('Overview');
        $summary = ($summary->length) ? $summary->item(0)->nodeValue : null;
        $guests = $dom->getElementsByTagName('GuestStars');
        $guests = array_filter(array_filter(explode('|', ($guests->length) ? $guests->item(0)->nodeValue : ''), 'trim'), 'strlen');
        $directors = $dom->getElementsByTagName('Directors');
        $directors = array_filter(array_filter(explode('|', ($directors->length) ? $directors->item(0)->nodeValue : ''), 'trim'), 'strlen');
        $writers = $dom->getElementsByTagName('Writer');
        $writers = array_filter(array_filter(explode('|', ($writers->length) ? $writers->item(0)->nodeValue : ''), 'trim'), 'strlen');
        $season = $dom->getElementsByTagName('SeasonNumber');
        $season = ($season->length) ? $season->item(0)->nodeValue : null;
        $number = $dom->getElementsByTagName('EpisodeNumber');
        $number = ($number->length) ? $number->item(0)->nodeValue : null;
        $rating = $dom->getElementsByTagName('Rating');
        $rating = ($rating->length) ? $rating->item(0)->nodeValue : null;
        $image = $dom->getElementsByTagName('filename');
        $image = ($image->length) ? $image->item(0)->nodeValue : null;
        return array(
            'name' => $name,
            'summary' => $summary,
            'season' => $season,
            'number' => $number,
            'guests' => $guests,
            'directors' => $directors,
            'writers' => $writers,
            'rating' => $rating,
            'image' => $image
        );
    }
}