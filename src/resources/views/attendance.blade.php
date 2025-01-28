@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
    <div class="contact-form__content">
        <h1 class="page-title">勤怠一覧</h1>
        <div class="contact-form__heading">
            <form class="change_month" action="/attendance/list" method="get">
                <button class="reverce_search_month" name="date" value="{{ $lastMonth }}">&#x279C;</button>
                <label class="month">前月</label>
            </form>
            <h2 class="title_month">{{ $thisMonth }}</h2>
            <form class="change_month" action="/attendance/list" method="get">
                <label class="month">翌月</label>
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
                                    @if(isset($attendance->approves) && $attendance->approves->isNotEmpty())
                                        <input type="hidden" name="attendance_id" value="{{ $attendance['id'] }}">
                                        <button type="submit" class="detail-btn" name="id" value="{{ $attendance->approves->first()->id }}">
                                            <label>詳細</label>
                                        </button>
                                    @else
                                        <button type="submit" class="detail-btn" name="id" value="{{ $attendance['id'] }}">
                                            <label>詳細</label>
                                        </button>
                                    @endif
                                </form>
                            </td>              
                        </tr>
                    @endif
                @endforeach              
            </table>
        </div>
    </div>
@endsection