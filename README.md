## 初期設定
### Google Maps PlatformのAPI_KEYを設定
`resources/js/googlemapsapi.js` 配下の`YOUR_API_KEY`を設定

### 環境ファイル作成
```sh 
$ cp .env.example .env
$ php artisan key:generate
```

### データベース作成
``` sql
CREATE DATABASE team21;
```

## ライブラリのインストール
``` sh
# サーバーライブラリのインストール(composer.json)
$ composer install

# フロントライブラリのインストール(package.json)
$ npm install
```
## 起動方法
``` sh
#開発デプロイ時
$ npm run dev

#本番デプロイ時
$ npm run prod

# Laravelの開発サーバーをlocalhostで立ち上げ
$ php artisan serve
```

## 便利コマンド
```　sh
# マイグレーション実行
$ php artisan migrate

# ルートを確認するためのコマンド
$ php artisan route:list
```

## カフェ情報 APIの叩き方
``` sh
# 取得(全件)
$ curl localhost:8000/api/cafe/
# 登録
$ curl -X POST http://localhost:8000/api/cafe/ -H "Content-Type: application/json" -d "{\"place_id\":\"afjw3891fa\",\"user_id\":1,\"outlet_count\":1,\"comment\":\"コメント\"}"
# 取得(1件)
$ curl localhost:8000/api/cafe/${id}
# 更新
$ curl -X PUT http://localhost:8000/api/cafe/${id} -H "Content-Type: application/json" -d "{\"outlet_count\":2,\"comment\":\"コメントコメント\"}"
# 削除
$ curl -X DELETE http://localhost:8000/api/cafe/${id}
```

## テスト
「Unit」テストディレクトリ内のテストはLaravelアプリケーションを起動しないため、アプリケーションのデータベースやその他のフレームワークサービスにアクセスできません。
「Feature」テストディレクトリ内のテストは、複数のオブジェクトが相互作用する方法や、JSONエンドポイントへの完全なHTTPリクエストなど、コードの広い部分をテストします。

テストを実行すると、Laravelはphpunit.xmlファイルで定義してある設定環境により、設定環境を自動的にtestingに設定します。
Laravelはまた、テスト中にセッションとキャッシュをarrayドライバに自動的に設定します。つまり、テスト中のセッションまたはキャッシュデータが保持されることはありません。
テストを実行する前は必ずconfig:clear Artisanコマンドを使用して設定のキャッシュをクリアしてください。

### テスト作成
```　sh
# 新しいテストケースを作成するには、make:test Artisanコマンドを使用します。デフォルトでは、テストはtests/Featureディレクトリへ配置されます。
$ php artisan make:test ${テストクラス名}Test

# tests/Unitディレクトリ内にテストを作成したい場合は、make:testコマンドを実行するときに--unitオプションを使用します。
$ php artisan make:test ${テストクラス名}Test --unit
```

### テスト実行
```　sh
# 設定のキャッシュをクリア
$ php artisan config:cache

# テスト用データベースのマイグレーション実行
$ php artisan migrate --env=testing

# テスト用データベースのシードをテスト環境に実行
$ php artisan db:seed --env=testing

# テスト実行
$ php artisan test
```

#### 参考資料
[テストの準備](https://readouble.com/laravel/8.x/ja/testing.html)

[JSON APIのテスト](https://readouble.com/laravel/8.x/ja/http-tests.html#JSON%20API%E3%81%AE%E3%83%86%E3%82%B9%E3%83%88)

[Laravelでテストをする前の準備、設定の事](https://tenrakatsuno.com/programing-note/laravel-test-database/)
