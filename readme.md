my Google Reviews
===



このプラグインは、Google の Place ID を使用してレビューを表示します。


```
get_google_reviews( $place_id )
```

ショートコード
```
[google_reviews place_id=$place_id]
```

Googleマイビジネスのデータを取得するには、APIを使用した独自の [API Key](https://developers.google.com/maps/documentation/places/web-service/place-id?hl=ja) が必要です。

1. 新しいプロジェクトを作成するか、Google Developer's Consoleで既存のプロジェクトを開きます。
2. Places APIを検索し、ボタンをクリックしてアカウントでこのAPIを有効にします。
3. 認証情報で「認証情報を作成」ボタンをクリックします。
4. オプションから「API Key」を選択します。
5. このキーが作成されたら、「閉じる」をクリックします。
6. 新しく作成したAPIキーを選択します。
7. 「アプリケーションの制限」の下で、次のように設定します： 「IPアドレス」と「項目の追加」にウェブサーバーの IP： xxx.xxx.xxx.xxx を設定します。
8. 「APIの制限」の下で、「キーの制限」を選択し、オプションのリストから Places API」だけを選択し、「OK」をクリックします。
9. 「保存」をクリックして制限を設定します。
10. この新しいAPI Keyをこの「設定」>「一般」>「Google Reviews Settings」の「API Key」に入力します。
11.最後に、定期的なリクエストのために、プロジェクトの課金を有効にして、実質的かつ無料のAPIリクエスト割り当てを受け取ってください。

Google の Place [ID Finder](https://developers.google.com/places/place-id) で事業所名を検索してください。
Google マップでビジネス名を編集することができます。

