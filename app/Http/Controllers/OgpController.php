<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OgpController extends Controller
{
    public function fetch(Request $request)
    {
        $url = $request->input('url');

        // URLが空だったらエラーを返す
        if (empty($url)) {
            return response()->json(['error' => 'URLを入力してください'], 400);
        }

        // URLの形式チェック（httpかhttpsで始まるものだけOK）
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return response()->json(['error' => '正しいURLを入力してください'], 400);
        }

        // httpとhttpsだけ許可（ftp://などをブロック）
        $scheme = parse_url($url, PHP_URL_SCHEME);
        if (!in_array($scheme, ['http', 'https'])) {
            return response()->json(['error' => 'httpまたはhttpsのURLを入力してください'], 400);
        }

        // localhost や内部IPアドレスをブロック
        $host = parse_url($url, PHP_URL_HOST);
        $blockedHosts = ['localhost', '127.0.0.1', '0.0.0.0', '::1'];
        if (in_array($host, $blockedHosts)) {
            return response()->json(['error' => '無効なURLです'], 400);
        }

        // URLのHTMLを取得する
        $html = @file_get_contents($url);

        // HTMLが取得できなかったらエラーを返す
        if ($html === false) {
            return response()->json(['error' => 'URLにアクセスできませんでした'], 400);
        }

        // OGP情報を取り出す
        $title = $this->getOgpValue($html, 'og:title')
            ?? $this->getTitleTag($html)
            ?? '';

        $image = $this->getOgpValue($html, 'og:image') ?? '';

        return response()->json([
            'title' => $title,
            'image' => $image,
        ]);
    }

    // <meta property="og:title" content="〇〇"> から値を取り出す
    private function getOgpValue($html, $property)
    {
        if (preg_match('/<meta[^>]*property=["\']' . preg_quote($property, '/') . '["\'][^>]*content=["\'](.*?)["\'][^>]*>/i', $html, $matches)) {
            return html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
        }
        // contentとpropertyの順番が逆のパターンにも対応
        if (preg_match('/<meta[^>]*content=["\'](.*?)["\'][^>]*property=["\']' . preg_quote($property, '/') . '["\'][^>]*>/i', $html, $matches)) {
            return html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
        }
        return null;
    }

    // OGPタイトルがない場合は<title>タグから取得する
    private function getTitleTag($html)
    {
        if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $matches)) {
            return html_entity_decode(trim($matches[1]), ENT_QUOTES, 'UTF-8');
        }
        return null;
    }
}
