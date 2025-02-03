@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin_attendance.css') }}">
@endsection

@section('content')
    <div class="contact-form__content">   
        <h1 class="page-title">{{ $user }}さんの勤怠</h1>
        <div class="contact-form__heading">
            <form class="change_month" action="/admin/attendance/staff/{id}" method="get">
                <input type="hidden" name="id" value="{{ $id }}">
                <input type="hidden" name="name" value="{{ $user }}">
                <button class="reverce_search_month" name="date" value="{{ $lastMonth }}">&#x279C;</button>
                <label class="month">前月</label>
            </form>
            <h2 class="title_month">{{ $thisMonth }}</h2>
            <form class="change_month" action="/admin/attendance/staff/{id}" method="get">
                <label class="month">翌月</label>
                <input type="hidden" name="id" value="{{ $id }}">
                <input type="hidden" name="name" value="{{ $user }}">
                <button class="search_month" name="date" value="{{ $nextMonth }}">&#x279C;</button>
            </form>
        </div>
        <div class="attend-table">
            <table class="attend-table__inner">
                <tr class="attend-table__row">
                    <th class="attend-table__header">日付</th>
                    <th class="attend-table__header">出勤</th>
                    <th class="attend-table__header">退勤</th>
                    <th class="attend-table__header">休憩</th>
                    <th class="attend-table__header">合計</th>
                    <th class="attend-table__header">詳細</th>
                </tr>
                @foreach ($attendances as $attendance)
                    @if (!empty($attendance->date) && empty($attendance->commute))
                        <p></p>
                    @else                  
                        <tr class="attend-table__row">                
                            <td class="attend-table__item">
                                {{ \Carbon\Carbon::parse($attendance['date'])->isoFormat("MM/DD(ddd)") }}
                            </td>
                            <td class="attend-table__item">
                                {{ substr($attendance['commute'], 0, 5) }}
                            </td>
                            <td class="attend-table__item">
                                {{ substr($attendance['leave'], 0, 5) }}
                            </td>
                            <td class="attend-table__item">
                                {{ preg_replace('/^0/', '', substr($attendance['break_time'], 0, 5)) }}
                            </td>
                            <td class="attend-table__item">
                                {{ preg_replace('/^0/', '', substr($attendance['work_time'], 0, 5)) }}
                            </td>
                            <td class="attend-table__item">
                                <form action="/attendance/{id}" method="get">
                                    <input type="hidden" name="id"  value="{{ $attendance['id'] }}">
                                    <button type="submit" class="detail-btn" name="date" value="{{ \Carbon\Carbon::parse($attendance['date'])->format('Y/m/d') }}">
                                        <label>詳細</label>
                                    </button>
                                </form>
                            </td>              
                        </tr>
                    @endif
                @endforeach                                  
            </table>
        </div>
        @php
            $csvButtonDisplayed = false;
        @endphp
        @foreach ($attendances as $attendance)
            @if (!empty($attendance->date) && empty($attendance->commute))
                <p></p>
            @else
                @if (!$csvButtonDisplayed)
                    <form action="{{'/export?'.http_build_query(request()->query())}}" method="post">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $attendance['user_id'] }}">
                        <button class="csv-btn" type="submit" name="date" value="{{ $thisMonth }}">CSV出力</button>
                    </form>
                    @php
                        $csvButtonDisplayed = true;
                    @endphp
                @endif
            @endif
        @endforeach    
    </div>
@endsection