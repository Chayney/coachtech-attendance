@extends('layouts.app')

@section('css')
    <link rel='stylesheet' href="{{ asset('css/index.css') }}">
@endsection

@section('content')
    <div class="container">
        @if (empty($attendance->commute))
            @if ($attendance->status_id == 1)
                <label class="status"><span>{{ $statuses->where('id', 1)->first()->name }}</span></label>
                <h2 class="date" id="current-date"></h2>
                <span class="current" id="current-time"></span>
                <form class="commute-btn" action="/commute" method="post">
                    @csrf
                    <input type="hidden" name="user_id">
                    <button class="commute" type="submit">出勤</button>
                </form>
            @endif
        @elseif (!empty($attendance->commute) && !empty($attendance->leave))
            @if ($attendance->status_id == 4)
                <label class="status"><span>{{ $statuses->where('id', 4)->first()->name }}</span></label>
                <h2 class="date" id="current-date"></h2>
                <span class="current" id="current-time"></span><br>
                <span class="done">お疲れ様でした。</span>
            @endif
        @elseif (!empty($attendance->commute) && !empty($rests->start_rest) && !empty($rests->end_rest))
            @if ($attendance->status_id == 2)
                <label class="status"><span>{{ $statuses->where('id', 2)->first()->name }}</span></label>
                <h2 class="date" id="current-date"></h2>
                <span class="current" id="current-time"></span>
                <div class="btn-group">
                    <form class="punch-btn" action="/leave" method="post">
                        @csrf
                        @method('PATCH')
                        <button class="punch" type="submit">退勤</button>
                    </form>
                    <form class="rest-btn" action="/rest/start" method="post">
                        @csrf
                        <button class="rest" type="submit">休憩入</button>
                    </form>
                </div>
            @endif
        @elseif (!empty($attendance->commute) && empty($rests->start_rest))
            @if ($attendance->status_id == 2)
                <label class="status"><span>{{ $statuses->where('id', 2)->first()->name }}</span></label>
                <h2 class="date" id="current-date"></h2>
                <span class="current" id="current-time"></span>
                <div class="btn-group">
                    <form class="punch-btn" action="/leave" method="post">
                        @csrf
                        @method('PATCH')
                        <button class="punch" type="submit">退勤</button>
                    </form>
                    <form class="rest-btn" action="/rest/start" method="post">
                        @csrf
                        <button class="rest" type="submit">休憩入</button>
                    </form>
                </div>
            @endif
        @elseif (!empty($rests->start_rest) && empty($rests->end_rest))
            @if ($attendance->status_id == 3)
                <label class="status"><span>{{ $statuses->where('id', 3)->first()->name }}</span></label>
                <h2 class="date" id="current-date"></h2>
                <span class="current" id="current-time"></span>
                <form class="break-btn" action="/rest/end" method="post">
                    @csrf
                    @method('PATCH')
                    <button class="break" type="submit">休憩戻</button>
                </form>
            @endif
        @endif
    </div>
    <script>
        function updateTime() {
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1);
            const day = String(now.getDate());
            const days = ["日", "月", "火", "水", "木", "金", "土"];
            const weekDay = days[now.getDay()];
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const currentDateElement = document.getElementById('current-date');
            currentDateElement.textContent = `${year}年${month}月${day}日(${weekDay})`;           
            const currentTimeElement = document.getElementById('current-time');
            currentTimeElement.textContent = `${hours}:${minutes}`;
        }
        setInterval(updateTime, 1000);
        updateTime();
    </script>
@endsection