<template>
    <div class="regInfo">
        <div class="title">
            <h1>電源登録</h1>
        </div>
        <br/><br/>
        <div class="regInfo-form">
            <div class="infoItem">
                <label class="label">店名</label><br/>
                <input
                    id="pac-input-a"
                    class="controls"
                    type="text"
                    placeholder="Search Box"
                />
            </div>
            <br/><br/>
            <div class="infoItem">
                <label class="label">電源の数</label><br/>
                <input
                    type="text"
                    id="outlet_count"
                    class="outlet_count"
                /><br/>
            </div>
            <br/><br/>
            <div class="infoItem">
                <label class="label">コメント</label><br/>
                <textarea cols="50" rows="10" id="comment"></textarea><br/>
            </div>
            <div>
                <input type="submit" value="登録" @click="registerInfo"/>
            </div>
        </div>
    </div>
</template>

<script>
import {Loader} from "@googlemaps/js-api-loader";
import {APIKEY} from "../googlemapsapi";
import axios from "axios";

export default {
    data() {
        return {
            loader: new Loader({
                apiKey: APIKEY,
                version: "weekly",
                libraries: ["places"], // 今回は検索機能を追加するのでPlacesライブラリを指定
                language: "ja",
            }),
            mapOptions: {
                center: {
                    // デフォルトのマップオプション(東京駅の経度・緯度
                    lat: 35.6809591,
                    lng: 139.7673068,
                },
                zoom: 15,
            },
            shopNmae: "",
            outlet_count: "",
            user_id: "1",
            shopComment: "",
        };
    },
    mounted() {
        // マップに検索ボックスを設置する
        this.loader
            .load() // Promise
            .then((google) => {
                if (navigator.geolocation) {
                    // ユーザーの端末がGeoLocation APIに対応しているかの判定
                    const input = document.getElementById("pac-input-a");
                    const options = {
                        componentRestrictions: {country: "jp"},
                        fields: [
                            "formatted_address",
                            "geometry",
                            "name",
                            "place_id",
                        ],
                        strictBounds: false,
                        types: ["establishment"],
                    };
                    const autocomplete = new google.maps.places.Autocomplete(
                        input,
                        options
                    );

                    // 検索候補が選択されたらplace_idを取得する
                    autocomplete.addListener("place_changed", () => {
                        const place = autocomplete.getPlace();

                        // place_idを取得する
                        this.place_id = place.place_id;
                    });
                    return;
                }
            })
            .catch((e) => {
                console.log(e);
            });
    },

    methods: {
        // 入力した情報をDBに登録するメソッド
        registerInfo() {
            this.outlet_count = document.getElementById("outlet_count").value;
            this.shopComment = document.getElementById("comment").value;

            this.handleInsert();
        },

        // 実際にDBに登録する処理
        handleInsert() {
            if (this.outlet_count && this.shopComment) {
                if (this.outlet_count.match(/[0-9]+/)) {
                    // Ajax
                    this.run_ajax("POST", "/api/cafe/", {
                        place_id: this.place_id,
                        user_id: this.user_id,
                        outlet_count: this.outlet_count,
                        comment: this.shopComment,
                    });

                    alert("データの登録が完了しました。");

                    // DBに登録が完了したら、dataの値を初期化する
                    this.place_id = "";
                    this.user_id = "";
                    this.outlet_count = "";
                    this.shopComment = "";
                } else {
                    alert("電源の数は数値で入力してください。");
                    this.outlet_count = "";
                }
            } else {
                alert("全ての項目を埋めてください。");
            }
        },

        run_ajax(method, url, data) {
            axios({
                method: method,
                baseURL: url,
                data: data,
                headers: {
                    // JSON
                    "Content-Type": "application/json",
                    // CSRFトークン
                    // ※AxiosはCookie(Laravelが自動生成)に「X-XSRF-TOKEN」があると、そのヘッダを自動的に送信するので以下は不要
                    // https://readouble.com/laravel/6.x/ja/csrf.html
                    //'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
            })
                .then((response) => {
                    this.status =
                        "サーバーからのメッセージ(" +
                        this.formatConversion(new Date()) +
                        ") ：" +
                        response.data.msg;
                    console.log(response);

                    // 新規登録時のみIDなどが返却される
                    if (response.data.id) {
                        // 失敗
                        if (response.data.id == "error") {
                            // エラー制御は行っていないので各自で。
                            // 成功
                        } else {
                            // 先頭にアイテムを追加する
                            this.items.unshift({
                                id: response.data.id,
                                name: response.data.name,
                                comment: response.data.comment,
                                updated_at: response.data.updated_at,
                            });
                            this.mode.unshift(false);
                        }

                        // 更新/削除
                    } else {
                        // エラー制御は行っていないので各自で。
                    }
                })
                .catch((err) => {
                    this.status = err.message;
                    console.log(err);
                });

        },
    },
};
</script>

<style>
.regInfo {
    text-align: center;
}

.regInfo-form {
    padding: 10px;
    margin: 0 auto;
    width: 500px;
    border: 3px solid rgb(63, 84, 177);
}

.infoItem {
    width: 400px;
    margin: 0 auto;
}

#pac-input-a {
    width: 357px;
}

.outlet_count {
    width: 357px;
}

.form-content {
    text-align: center;
}

.label {
    /* text-align: left; */
    font-weight: bold;
}
</style>
