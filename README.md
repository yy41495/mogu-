# mogu+（もぐプラス）

## 1. 概要

### mogu+（もぐプラス）

> **「作りたいレシピを、自分だけのノートに。」**

Web上で見つけたレシピのURLを入力するだけで、タイトルとサムネイル画像を自動取得してレシピを保存できる、**個人用レシピ管理Webアプリ**です。

Googleアカウントでログインするだけですぐ使えます。材料・手順・タグを自由に管理でき、キーワードやタグで絞り込み検索も可能です。

 **アプリURL：** https://mogu-plus.onrender.com/


| PC版 | スマートフォン版 |
|------|----------------|
| <img width="45%" alt="Image" src="https://github.com/user-attachments/assets/6bff3d09-5640-4725-ab3f-c246815e3764" /> | <img width="45%" alt="Image" src="https://github.com/user-attachments/assets/aeeaef5b-6f73-4524-83a7-4112e0826b02" /> |

---

## 2. 使用技術

### フロントエンド
| 技術 | 用途 |
|------|------|
| HTML / CSS | マークアップ・スタイリング |
| JavaScript (ES Modules) | OGP自動取得・動的フォーム操作・画像プレビューなど |
| Vite | フロントエンドビルドツール（JS/CSSのバンドル・HMR） |

### バックエンド
| 技術 | 用途 |
|------|------|
| PHP 8.x | サーバーサイドプログラミング言語 |
| Laravel 11 | PHPフレームワーク（MVC構成・ルーティング・認証など） |
| Laravel Socialite | Google OAuth 2.0 認証 |

### データベース
| 技術 | 用途 |
|------|------|
| PostgreSQL | リレーショナルデータベース |

### インフラ・外部サービス
| 技術 | 用途 |
|------|------|
| Render | アプリケーション・DBホスティング（デプロイ） |
| Cloudinary | レシピ画像のクラウドストレージ・最適化配信 |
| Google OAuth 2.0 | ログイン認証 |
| Docker / Laravel Sail | ローカル開発環境のコンテナ化 |

---

## 3. インフラ構成図

<img width="1841" height="724" alt="Image" src="https://github.com/user-attachments/assets/a898d740-dcf1-4760-a64a-9e41fff7becf" />

### システム構成図

```
[ユーザー（ブラウザ）]
       │
       ▼
[Render（Webサーバー）]
  Laravel (PHP)
  ├─ Google OAuth 2.0 ─── [Google認証サーバー]
  ├─ OGP取得（PHPでURL先のHTMLを解析）
  ├─ 画像アップロード ──── [Cloudinary（画像ストレージ）]
  └─ データ保存・取得 ──── [PostgreSQL（Supabase）]
```

---

## 4. 機能一覧

### 機能要件

| 機能 | 説明 |
|------|------|
| **Googleログイン** | Google OAuth 2.0 を使ったソーシャルログイン。パスワード不要で利用開始できる |
| **レシピ一覧表示** | ログインユーザー自身のレシピのみ表示。新しい順に並ぶ |
| **レシピ作成** | タイトル・メモ・参照URL・画像・タグ・材料・手順を登録できる |
| **レシピ編集** | 既存レシピの全項目を編集できる |
| **レシピ削除** | ソフトデリート（論理削除）により、DBからは見えなくなるが履歴は保持される |
| **レシピ詳細表示** | タグ・材料・手順をまとめて確認できる詳細ページ |
| **OGP自動取得** | 参照URLを入力してボタンを押すと、そのページのタイトルとサムネイル画像を自動取得・入力 |
| **画像アップロード** | Cloudinaryに画像を保存。アップロード時に自動リサイズ・最適化 |
| **タグ管理** | 既存タグの選択 または 新規タグ（名前＋カラー）の作成・紐付けが可能 |
| **材料・手順管理** | 動的にフィールドを追加・削除できるフォームで、複数の材料・手順を登録 |
| **キーワード検索** | タイトル・メモ・タグ名・材料名を横断して部分一致検索（大文字小文字を区別しない） |
| **タグフィルター** | 複数のタグを選択してレシピを絞り込める |

### 非機能要件

| 項目 | 内容 |
|------|------|
| **セキュリティ（認証）** | `auth` ミドルウェアにより、未ログインユーザーはレシピ画面にアクセス不可 |
| **セキュリティ（認可）** | レシピ取得・更新・削除時は `user_id` でフィルタリングし、他ユーザーのデータへのアクセスを防止 |
| **セキュリティ（CSRF対策）** | LaravelのCSRFトークンをすべてのPOSTリクエストに付与 |
| **セキュリティ（SSRF対策）** | OGP取得時にlocalhost・内部IPアドレス・http/https以外のスキームをブロック |
| **セキュリティ（入力検証）** | Laravelのバリデーションで全フォーム入力を検証 |
| **レスポンシブ対応** | スマートフォン・タブレット・PCに対応したレイアウト |
| **画像最適化** | Cloudinaryで幅・高さ最大1200px・品質自動・フォーマット自動変換 |
| **データ保全** | ソフトデリートにより削除済みレシピのデータも保持 |
| **環境分離** | Docker / Laravel Sail によるローカル開発環境と本番環境の分離 |



===============================================================================


<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
