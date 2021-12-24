<template>
    <div class="regInfo">
        <div class="title">
            <h1>cafe 電源２</h1>
        </div>
        <a href="/register">会員登録</a> |
        <a href="/login">ログイン</a>
        <input id="pac-input" class="controls" type="text" placeholder="Search Box"/>
        <div id="map" style="height:500px; width:1100px;"></div>
        <post/>
    </div>
</template>

<script>
/* eslint-disable */
import {Loader} from '@googlemaps/js-api-loader';
import {APIKEY} from './googlemapsapi';
import axios from 'axios';
import Post from './view/Post.vue'

export default {
    components: {
        Post
    },
    data() {
        return {
            loader: new Loader({
                apiKey: APIKEY,
                version: "weekly",
                libraries: ["places"], // 今回は検索機能を追加するのでPlacesライブラリを指定
                language: "ja"
            }),
            mapOptions: {
                center: { // デフォルトのマップオプション(東京駅の経度・緯度
                    lat: 35.6809591,
                    lng: 139.7673068
                },
                zoom: 15
            },
            placeId: ""
        };
    },
    mounted() {
        this.loader.load() // Promise
            .then((google) => {
                if (navigator.geolocation) { // ユーザーの端末がGeoLocation APIに対応しているかの判定
                    navigator.geolocation.getCurrentPosition(this.success, this.error);
                    return;
                }
                this.createMap(this.mapOptions)
                // new google.maps.Map(document.getElementById("map"), mapOptions);
            })
            .catch(e => {
                console.log(e)
            });
    },
    methods: {
        success(position) {
            // 現在地の緯度・経度を取得
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;
            const center = {lat: latitude, lng: longitude}

            // マップ作成
            const mapOptions = {zoom: 15, center: center};
            const map = this.createMap(mapOptions)

            // 検索ボックスの初期化
            this.initSearchBox(map)
        },

        error(error) {
            // エラーコード(error.code)の番号
            // 0:UNKNOWN_ERROR  原因不明のエラー
            // 1:PERMISSION_DENIED  利用者が位置情報の取得を許可しなかった
            // 2:POSITION_UNAVAILABLE 電波状況などで位置情報が取得できなかった
            // 3:TIMEOUT  位置情報の取得に時間がかかり過ぎた
            console.log(error.code)
        },

        // マップ作成
        createMap(mapOptions) {
            return new google.maps.Map(document.getElementById('map'), mapOptions);
        },

        // マーカーの作成
        createMarker(markerOptions) {
            return new google.maps.Marker(markerOptions);
        },

        // 吹き出しの作成
        createInfoWindow(infoWindowOptions) {
            return new google.maps.InfoWindow(infoWindowOptions);
        },

        // 吹き出しのコンテンツ内容を作成
        createInfoWindowContent(place) {
            return `<div id="content">
                <div id="siteNotice"></div>
                <h1 id="firstHeading" class="firstHeading">${place.name}</h1>
                <div id="bodyContent">
                    <p>${place.geometry.location}</p>
                </div>
            </div>`;
        },

        // マーカーの吹き出しのコンテンツ内容を作成
        createInfoWindowMarkerContent(shopName, shopComment, phone_number, address) {
            return `<div id="content">
                <div id="siteNotice"></div>
                <h1 id="firstHeading" class="firstHeading">${shopName}</h1>
                <div id="bodyContent">
                    <p>${phone_number}</p>
                    <p>${address}</p>
                    <p>${shopComment}</p>
                </div>
            </div>`;
        },

        // 検索ボックスの初期化
        initSearchBox(map) {
            // マップに検索ボックスを設置する
            const input = document.getElementById("pac-input");
            const searchBox = new google.maps.places.SearchBox(input);

            const latlng = [];
            const registerMarkerOptions = [];
            const regInfoWindowOptions = [];
            const regInfoWindow = [];

            // マップの左上に設置
            map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
            // bounds_changedは地図のビューポート(見えている範囲)に変化があった時に発火します。
            map.addListener("bounds_changed", () => {
                searchBox.setBounds(map.getBounds());　//getBoundsメソッドはビューポートの境界を取得
            });

            let markers = []; // 検索BOXからのマーカーの配列
            let regMarkers = []; // DBに登録済みからのマーカーの配列

            // APIを叩く
            axios.get('/api/cafe/').then(response =>
                    // DBのplace_idをもとにマーカーを表示する
                {
                    for (let i = 0; i < response['data']['data']['cafeInfo'].length; i++) {

                        axios.get('https://maps.googleapis.com/maps/api/place/details/json?place_id=' + response['data']['data']['cafeInfo'][i]['place_id'] + '&key=' + APIKEY + '').then(res => {
                            latlng[i] = new google.maps.LatLng(res['data']['result']['geometry']['location']['lat'], res['data']['result']['geometry']['location']['lng']);
                            let mopts = {
                                position: latlng[i],
                                map: map,
                                icon: {
                                    fillColor: "blue",
                                    fillOpacity: 0.8,
                                    path: google.maps.SymbolPath.CIRCLE,
                                    scale: 16,
                                    strokeColor: "blue",
                                    strokeWeight: 1.0
                                }
                            }
                            registerMarkerOptions[i] = new google.maps.Marker(mopts);
                            // マーカーをクリックしたときに吹き出しに表示する内容
                            regInfoWindowOptions[i] = {content: this.createInfoWindowMarkerContent(res['data']['result']['name'], response['data']['data']['cafeInfo'][i]['comment'], res['data']['result']['formatted_phone_number'], res['data']['result']['formatted_address'])} // 吹き出しに表示する内容
                            regInfoWindow[i] = new google.maps.InfoWindow(regInfoWindowOptions[i]);
                            // マーカーをクリックしたときイベントを登録
                            registerMarkerOptions[i].addListener('click', () => {
                                regInfoWindow[i].open(map, registerMarkerOptions[i]); // 吹き出しの表示
                            });
                            regMarkers.push(registerMarkerOptions[i]);
                        })
                    }
                }
            )

            // 検索候補がクリックされた際のイベントを定義
            searchBox.addListener("places_changed", () => {
                // 選択された場所の情報を取得する
                const places = searchBox.getPlaces();
                if (places.length == 0) {
                    return;
                }
                // 古いマーカーを削除
                markers.forEach((marker) => {
                    marker.setMap(null);
                });
                markers = [];
                // LatLngBoundsクラスは境界(Bounding box)のインスタンスを作成するためのクラスです。
                // 境界とは、範囲のことです。インスタンスを作る時に引数で境界の左下と右上の位置座標を指定できますが、これらの引数は省略可能で空の境界を作ることもできます。
                // このインスタンスは、任意の数の位置座標を含み、それら全ての位置座標を包める最小の矩形が境界となります。
                // https://lab.syncer.jp/Web/API/Google_Maps/JavaScript/LatLngBounds/
                // geometry
                //   location(緯度・経度)
                //   viewport(見えている範囲)
                //   bounds(境界ボックス)
                const bounds = new google.maps.LatLngBounds();
                places.forEach((place) => {
                    // 結果の場所にジオメタリの情報がない イコール 検索候補に繋がらならないテキストを入力し、Enterキーを押した
                    if (!place.geometry || !place.geometry.location) {
                        console.log("Returned place contains no geometry");
                        return;
                    }
                    // ユーザーが検索候補を実際にクリックした場合、以下を実行
                    // 各場所にマーカーを追加
                    const markerOptions = {map, title: place.name, position: place.geometry.location}
                    const marker = this.createMarker(markerOptions);

                    // マーカーをクリックしたときに吹き出しに表示する内容
                    const infoWindowOptions = {content: this.createInfoWindowContent(place)} // 吹き出しに表示する内容
                    const infoWindow = this.createInfoWindow(infoWindowOptions);
                    // マーカーをクリックしたときイベントを登録
                    marker.addListener('click', () => {
                        infoWindow.open(map, marker); // 吹き出しの表示
                    });
                    markers.push(marker);

                    if (place.geometry.viewport) {
                        // 自身の境界に、指定した境界を取り込んで合成します。
                        // https://lab.syncer.jp/Web/API/Google_Maps/JavaScript/LatLngBounds/union/
                        bounds.union(place.geometry.viewport);
                    } else {
                        // 自身の境界に、新しく位置座標を追加します。境界の外の位置座標を追加すれば、境界が必要最低限に拡張します。
                        // https://lab.syncer.jp/Web/API/Google_Maps/JavaScript/LatLngBounds/extend/
                        bounds.extend(place.geometry.location);
                    }
                });
                map.fitBounds(bounds); // 指定した境界(範囲)がちょうどよく見えるように、地図のビューポート(位置座標とズーム値)を変更してくれます。
            });
        }
    }
};

</script>

<style>
/* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
#map {
    height: 100%;
    margin: 0 auto;
}

/* Optional: Makes the sample page fill the window. */
html,
body {
    height: 100%;
    margin: 0;
    padding: 0;
}

#description {
    font-family: Roboto;
    font-size: 15px;
    font-weight: 300;
}

#infowindow-content .title {
    font-weight: bold;
}

#infowindow-content {
    display: none;
}

#map #infowindow-content {
    display: inline;
}

.pac-card {
    margin: 10px 10px 0 0;
    border-radius: 2px 0 0 2px;
    box-sizing: border-box;
    -moz-box-sizing: border-box;
    outline: none;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
    background-color: #fff;
    font-family: Roboto;
}

#pac-container {
    padding-bottom: 12px;
    margin-right: 12px;
}

.pac-controls {
    display: inline-block;
    padding: 5px 11px;
}

.pac-controls label {
    font-family: Roboto;
    font-size: 13px;
    font-weight: 300;
}

#pac-input {
    background-color: #fff;
    font-family: Roboto;
    font-size: 15px;
    font-weight: 300;
    margin-left: 12px;
    padding: 0 11px 0 13px;
    text-overflow: ellipsis;
    width: 400px;
}

#pac-input:focus {
    border-color: #4d90fe;
}

.regInfo {
    text-align: center;
}

#title {
    color: #fff;
    background-color: #4d90fe;
    font-size: 25px;
    font-weight: 500;
    padding: 6px 12px;
}

#target {
    width: 345px;
}
</style>
