@if ($errors->any())
    <div {{ $attributes }}>
    <!-- ↓日本語に修正した -->
        <div class="font-medium text-red-600">エラーが発生しました！</div>

        <ul class="mt-3 list-disc list-inside text-sm text-red-600">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
