<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <!-- マイページ -->
        </h2>
    </x-slot>

    @section('content')
        <h1>カフェ情報を登録する</h1>
        {{ Form::open(['route' => 'cafe.store']) }}
            <div class='form-group'>
                {{ Form::label('comment', 'Comment') }}
                {{ Form::text('comment', null) }}
            </div>

</x-app-layout>
