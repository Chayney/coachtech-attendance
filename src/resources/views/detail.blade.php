@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
    <div class="contact-form__content">
        <h1>勤怠詳細</h1>
        <div class="attend-table">
            <table class="attend-table__inner">
                @foreach ($attendances as $attendance)
                    <tr class="attend-table__row">
                        <td class="attend-table__header_adjust"></td>
                        <th class="attend-table__header">名前</th>          
                        <td class="attend-table__item">
                            {{ $attendance['user']['name'] }}
                        </td>
                        <td class="attend-table__item_middle"></td>
                        <td class="attend-table__item"></td>
                        <td class="attend-table__item_adjust"></td>
                    </tr>
                    <tr class="attend-table__row">
                        <td class="attend-table__header_adjust"></td>
                        <th class="attend-table__header">日付</th>         
                        <td class="attend-table__item">
                            {{ \Carbon\Carbon::parse($attendance['date'])->isoFormat("YYYY年") }}
                        </td>
                        <td class="attend-table__item_middle"></td>
                        <td class="attend-table__item">
                            {{ \Carbon\Carbon::parse($attendance['date'])->isoFormat("M月D日") }}
                        </td>
                        <td class="attend-table__item_adjust"></td>
                    </tr>
                    <tr class="attend-table__row">
                        <td class="attend-table__header_adjust"></td>
                        <th class="attend-table__header">出勤・退勤</th>          
                        <td class="attend-table__item">
                            {{ substr($attendance['commute'], 0, 5) }}
                        </td>
                        <td class="attend-table__item_middle">
                            ~
                        </td>
                        <td class="attend-table__item">
                            {{ substr($attendance['leave'], 0, 5) }}
                        </td>
                        <td class="attend-table__item_adjust"></td>
                    </tr>     
                    <tr class="attend-table__row">
                        <td class="attend-table__header_adjust"></td>
                        <th class="attend-table__header">休憩</th>
                        @foreach ($rests as $rest)          
                            <td class="attend-table__item">
                                {{ substr($rest['start_rest'], 0, 5) }}
                            </td>
                            <td class="attend-table__item_middle">
                                ~
                            </td>
                            <td class="attend-table__item">
                                {{ substr($rest['end_rest'], 0, 5) }}
                            </td>
                            <td class="attend-table__item_adjust"></td>
                        @endforeach
                    </tr>
                    <tr class="attend-table__row">
                        <td class="attend-table__header_adjust"></td>
                        <th class="attend-table__header">備考</th>          
                        <td class="attend-table__item" colspan="3">
                            <input type="text" class="other">
                        </td>
                        <td class="attend-table__item_middle"></td>
                        <td class="attend-table__item"></td>
                        <td class="attend-table__item_adjust"></td>
                    </tr>
                @endforeach            
            </table>
        </div>
    </div>
@endsection