@extends('layouts.app')

@section('css')
    <link rel='stylesheet' href="{{ asset('css/index.css') }}">
@endsection

@section('content')
    <div class="container">
        @if (empty($attendances->commute))
            <label class="status"><span>勤務外</span></label>
            <h2 class="date">{{ \Carbon\Carbon::now()->isoFormat("YYYY年MM月DD日(ddd)") }}</h2>
            <span class="current">{{ \Carbon\Carbon::now()->format("H:i") }}</span>
            <form class="commute-btn" action="/commute" method="post">
                @csrf
                <input type="hidden" name="user_id">
                <button class="commute" type="submit">出勤</button>
            </form>
        @elseif (!empty($attendances->commute) && !empty($attendances->leave) && !empty($rests->start_rest) && !empty($rests->end_rest))
            <label class="status"><span>退勤済</span></label>
            <h2 class="date">{{ \Carbon\Carbon::now()->isoFormat("YYYY年MM月DD日(ddd)") }}</h2>
            <span class="current">{{ \Carbon\Carbon::now()->format("H:i") }}</span><br>
            <span class="done">お疲れ様でした。</span>
        @elseif (!empty($attendances->commute) && !empty($rests->start_rest) && !empty($rests->end_rest))
            <label class="status"><span>出勤中</span></label>
            <h2 class="date">{{ \Carbon\Carbon::now()->isoFormat("YYYY年MM月DD日(ddd)") }}</h2>
            <span class="current">{{ \Carbon\Carbon::now()->format("H:i") }}</span>
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
        @elseif (!empty($attendances->commute) && empty($rests->start_rest))
            <label class="status"><span>出勤中</span></label>
            <h2 class="date">{{ \Carbon\Carbon::now()->isoFormat("YYYY年MM月DD日(ddd)") }}</h2>
            <span class="current">{{ \Carbon\Carbon::now()->format("H:i") }}</span>
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
        @elseif (!empty($rests->start_rest) && empty($rests->end_rest))
            <label class="status"><span>休憩中</span></label>
            <h2 class="date">{{ \Carbon\Carbon::now()->isoFormat("YYYY年MM月DD日(ddd)") }}</h2>
            <span class="current">{{ \Carbon\Carbon::now()->format("H:i") }}</span>
            <form class="break-btn" action="/rest/end" method="post">
                @csrf
                @method('PATCH')
                <button class="break" type="submit">休憩戻</button>
            </form>
        @endif
    </div>
@endsection