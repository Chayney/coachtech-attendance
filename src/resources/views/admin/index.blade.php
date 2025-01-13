@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin-index.css') }}">
@endsection

@section('content')
    <div class="contact-form__content">
        <h1 class="page-title">{{ $today->format('Y年n月j日') }}の勤怠</h1>
        <div class="contact-form__heading">
            <form class="change_month" action="/admin/attendance/list" method="get">
                <button class="reverce_search_month" name="date" value="{{ $yesterday->format('Y/m/d') }}">&#x279C;</button>
                <label class="month">前日</label>
            </form>
            <h2 class="title_month">{{ $today->format('Y/m/d') }}</h2>
            <form class="change_month" action="/admin/attendance/list" method="get">
                <button class="search_month" name="date" value="{{ $tomorrow->format('Y/m/d') }}">&#x279C;</button>
                <label class="month">翌日</label>
            </form>
        </div>
        <div class="attend-table">
            <table class="attend-table__inner">
                <tr class="attend-table__row">
                    <th class="attend-table__header">名前</th>
                    <th class="attend-table__header">出勤</th>
                    <th class="attend-table__header">退勤</th>
                    <th class="attend-table__header">休憩</th>
                    <th class="attend-table__header">合計</th>
                    <th class="attend-table__header">詳細</th>
                </tr>
                @foreach ($attendances as $attendance)
                    <tr class="attend-table__row">                
                        <td class="attend-table__item">
                            {{ $attendance['user']['name'] }}
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
                                <button type="submit" class="detail-btn" name="date" value="{{ $today->format('Y/m/d') }}">
                                    <label>詳細</label>
                                </button>
                            </form>
                        </td>              
                    </tr>
                @endforeach              
            </table>
        </div>
    </div>
@endsection