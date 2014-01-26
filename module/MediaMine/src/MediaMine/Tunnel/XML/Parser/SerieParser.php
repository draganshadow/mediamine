<?php
namespace MediaMine\Tunnel\XML\Parser;

class SerieParser
{
    public function parse($path) {
        $dom = new \DomDocument();
        $dom->load($path);
        $name = $dom->getElementsByTagName('SeriesName');
        $name = ($name->length) ? $name->item(0)->nodeValue : null;
        $summary = $dom->getElementsByTagName('Overview');
        $summary = ($summary->length) ? $summary->item(0)->nodeValue : null;
        $actors = $dom->getElementsByTagName('Actors');
        $actors = array_filter(array_filter(explode('|', ($actors->length) ? $actors->item(0)->nodeValue : ''), 'trim'), 'strlen');
        $genres = $dom->getElementsByTagName('Genre');
        $genres = array_filter(array_filter(explode('|', ($genres->length) ? $genres->item(0)->nodeValue : ''), 'trim'), 'strlen');
        $runtime = $dom->getElementsByTagName('Runtime');
        $runtime = ($runtime->length) ? $runtime->item(0)->nodeValue : null;
        $rating = $dom->getElementsByTagName('Rating');
        $rating = ($rating->length) ? $rating->item(0)->nodeValue : null;
        return array(
            'name' => $name,
            'summary' => $summary,
            'actors' => $actors,
            'genre' => $genres,
            'runtime' => $runtime,
            'rating' => $rating,
            'originalName' => $name,
            'productionYear' => 1900
        );
    }
}