# coachtech-attendance
「COACHTECH」はcoachtech勤怠管理アプリです。1日の出退勤登録と過去の勤怠情報を確認することが出来ます。

## 作成した目的
Laravel学習のまとめとして作成いたしました。提示された要件や成果物のイメージをもとに設計・コーディングを行いました。

## アプリケーションURL
デプロイしていないためURLはありません

## 他のリポジトリ
ありません

## 使用技術
1. PHP 7.4.9
2. Laravel v8.83.29
3. mysql:8.0.26
4. Fortify
5. JavaScript
6. Laravel-Permission

## 機能一覧
・会員登録機能→名前、メールアドレス、パスワード、パスワード確認が入力項目となっております。  
・ログイン機能→メールアドレス、パスワードでログイン出来、ログアウト機能もついています。  
・出勤機能→当日の出勤を登録出来ます。  
・退勤機能→当日の退勤を登録出来ます。  
・休憩機能→当日の休憩を登録出来ます。  
・勤怠情報修正機能→自身の勤怠情報の修正が出来ます。  
・テスト機能→PHPUnitテストが出来ます。

### 管理者専用機能
・承認機能→管理者専用修正申請承認ページよりユーザーの勤怠情報の承認が可能です。  
・スタッフ一覧閲覧機能→管理者専用スタッフ一覧ページより管理者を除く全ユーザーの名前とメールアドレスの確認が出来、スタッフ別の勤怠一覧も閲覧可能です。  
・CSV出力機能→スタッフ別の1ヶ月の勤怠情報をCSVで出力することが可能です。

## 機能に関する注意点 
・一般ユーザーは自身の勤怠詳細ページより情報修正を行うと管理者からの承認が下りない限り修正出来ませんのでもし誤修正してしまった場合は管理者にお問い合わせください。   
・情報修正に備考欄の入力も必須になっているため入力漏れがないようお願いいたします。  
・管理者も一般ユーザーの勤怠情報の修正が可能となっております。  
・日付の入力は先頭に0を付けて入力すると入力エラーメッセージが表示されるため0を付けないようお願いいたします(例:1月1日)。  
・休憩は1日に何回でも登録出来ますが出勤は1日1回のみの登録となっております。  
・休憩の有無に関わらず勤怠情報詳細ページに休憩の入力フォームはありますので該当日付の正しい勤務情報の入力をお願いいたします。  
・PHPUnitテストを実行した場合statusesテーブルのダミーデータが全て削除されていることがありますのでテスト実行後はシーディングを実行してください。

## ダミーデータの説明
1.	管理者  
・名前: admin  
・メールアドレス: admin@admin.com  
・パスワード: password  
2.	テストユーザー  
・名前: test  
・メールアドレス: test@example.com  
・パスワード: password

## テーブル設計
![Image](https://github.com/user-attachments/assets/2f520c16-e818-4977-a332-222ad314d5bc)  
![Image](https://github.com/user-attachments/assets/df36be92-e31e-4395-8fbc-4d3fbacfdbe5)  
![Image](https://github.com/user-attachments/assets/77e53916-d985-4363-b19c-1f185620042b)  
![Image](https://github.com/user-attachments/assets/06139f78-ef7c-40d6-b54f-b071ae8164fa)  
![Image](https://github.com/user-attachments/assets/4b07b4fd-d037-452c-b5a4-3e510bb1b599)

## ER図
![Image](https://github.com/user-attachments/assets/0a9ea6f5-8e67-422c-8bbc-9086bfafa73a)

## 環境構築

### コマンドライン上
$docker-compose up -d --build  
$docker-compose exec php bash

### PHPコンテナ内
$composer install

### src上
$cp .env.example .env

### PHPコンテナ内
$php artisan key:generate  
$php artisan migrate  
$php artisan db:seed

## PHPUnitテスト

### PHPコンテナ内
$php artisan test

## URL
・開発環境: http://localhost/login  
・phpMyAdmin: http://localhost:8080/
