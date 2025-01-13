@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin-approve.css') }}">
@endsection

@section('content')
    <div class="contact-form__content">
        <h1 class="page-title">勤怠詳細</h1>
        @foreach ($attendances as $attendance)
            @if ($attendance->status == '承認済み')
                <div class="attend-table">
                    <table class="attend-table__inner">                      
                        <tr class="attend-table__row">
                            <td class="attend-table__header_adjust"></td>
                            <th class="attend-table__header">名前</th>          
                            <td class="attend-table__item">
                                {{ $attendance['approveUser']['name'] }}
                            </td>
                            <td class="attend-table__item_middle"></td>
                            <td class="attend-table__item"></td>
                            <td class="attend-table__item_adjust"></td>
                        </tr>
                        <tr class="attend-table__row">
                            <td class="attend-table__header_adjust"></td>
                            <th class="attend-table__header">日付</th>       
                            <td class="attend-table__item">
                                <label class="attend-form">{{ \Carbon\Carbon::parse($attendance['approveAttendance']['date'])->isoFormat("YYYY年") }}</label>
                            </td>
                            <td class="attend-table__item_middle"></td>
                            <td class="attend-table__item">
                                <label class="attend-form">{{ \Carbon\Carbon::parse($attendance['approveAttendance']['date'])->isoFormat("M月D日") }}</label>
                            </td>
                            <td class="attend-table__item_adjust"></td>
                        </tr>
                        <tr class="attend-table__row">
                            <td class="attend-table__header_adjust"></td>
                            <th class="attend-table__header">出勤・退勤</th>          
                            <td class="attend-table__item" colspan="3">
                                <label class="attend-form">{{ substr($attendance['approveAttendance']['commute'], 0, 5) }}</label>
                                <label class="approve-middle">~</label>
                                <label class="attend-form">{{ substr($attendance['approveAttendance']['leave'], 0, 5) }}</label>
                            </td>
                            <td class="attend-table__item_adjust"></td>
                        </tr>
                        @if(count($rests) > 1)
                            @foreach ($rests as $rest)     
                                <tr class="attend-table__row">
                                    <td class="attend-table__header_adjust"></td>
                                    @if($loop->iteration > 1)
                                        <th class="attend-table__header">休憩{{ $loop->iteration }}</th>
                                    @else
                                        <th class="attend-table__header">休憩</th>
                                    @endif                               
                                    <td class="attend-table__item" colspan="3">
                                        <label class="attend-form">{{ $rest['start_rest'] }}</label>
                                        <label class="approve-middle">~</label>
                                        <label class="attend-form">{{ $rest['end_rest'] }}</label>                     
                                    </td>
                                    <td class="attend-table__item_adjust"></td>                           
                                </tr>
                            @endforeach
                        @elseif(count($rests) == 1)
                            @foreach ($rests as $rest)     
                                <tr class="attend-table__row">
                                    <td class="attend-table__header_adjust"></td>
                                    <th class="attend-table__header">休憩</th>                             
                                    <td class="attend-table__item" colspan="3">
                                        <label class="attend-form">{{ substr($rest['start_rest'], 0, 5) }}</label>
                                        <label class="approve-middle">~</label>
                                        <label class="attend-form">{{ substr($rest['end_rest'], 0, 5) }}</label>
                                    </td>
                                    <td class="attend-table__item_adjust"></td>                           
                                </tr>
                            @endforeach
                        @else
                            <p></p>
                        @endif
                        <tr class="attend-table__row">
                            <td class="attend-table__header_adjust"></td>
                            <th class="attend-table__header">備考</th>          
                            <td class="attend-table__item" colspan="3">
                                {{ $attendance['approveAttendance']['reason'] }}
                            </td>
                            <td class="attend-table__item_adjust"></td>
                        </tr>                              
                    </table>
                </div>
                <button class="approved">承認済み</button>
            @else
                <form action="/approve/update" method="post">
                    @csrf
                    @method('PATCH')
                    <div class="attend-table">
                        <table class="attend-table__inner">                      
                            <tr class="attend-table__row">
                                <td class="attend-table__header_adjust"></td>
                                <th class="attend-table__header">名前</th>          
                                <td class="attend-table__item">
                                    {{ $attendance['approveUser']['name'] }}
                                </td>
                                <td class="attend-table__item_middle"></td>
                                <td class="attend-table__item"></td>
                                <td class="attend-table__item_adjust"></td>
                            </tr>
                            <tr class="attend-table__row">
                                <td class="attend-table__header_adjust"></td>
                                <th class="attend-table__header">日付</th>         
                                <td class="attend-table__item">
                                    {{ \Carbon\Carbon::parse($attendance['approveAttendance']['date'])->isoFormat("YYYY年") }}
                                </td>
                                <td class="attend-table__item_middle"></td>
                                <td class="attend-table__item">
                                    {{ \Carbon\Carbon::parse($attendance['approveAttendance']['date'])->isoFormat("M月D日") }}
                                </td>
                                <td class="attend-table__item_adjust"></td>
                            </tr>
                            <tr class="attend-table__row">
                                <td class="attend-table__header_adjust"></td>
                                <th class="attend-table__header">出勤・退勤</th>          
                                <td class="attend-table__item" colspan="3">
                                    <label class="attend-form">{{ substr($attendance['approveAttendance']['commute'], 0, 5) }}</label>
                                    <label class="approve-middle">~</label>
                                    <label class="attend-form">{{ substr($attendance['approveAttendance']['leave'], 0, 5) }}</label>
                                </td>
                                <td class="attend-table__item_adjust"></td>
                            </tr>
                            @if(count($rests) > 1)
                                @foreach ($rests as $rest)     
                                    <tr class="attend-table__row">
                                        <td class="attend-table__header_adjust"></td>
                                        @if($loop->iteration > 1)
                                            <th class="attend-table__header">休憩{{ $loop->iteration }}</th>
                                        @else
                                            <th class="attend-table__header">休憩</th>
                                        @endif                               
                                        <td class="attend-table__item" colspan="3">
                                            <label class="attend-form">{{ substr($rest['start_rest'], 0, 5) }}</label>
                                            <label class="approve-middle">~</label>
                                            <label class="attend-form">{{ substr($rest['end_rest'], 0, 5) }}</label>
                                        </td>
                                        <td class="attend-table__item_adjust"></td>                           
                                    </tr>
                                @endforeach
                            @elseif(count($rests) == 1)
                                @foreach ($rests as $rest)     
                                    <tr class="attend-table__row">
                                        <td class="attend-table__header_adjust"></td>
                                        <th class="attend-table__header">休憩</th>                             
                                        <td class="attend-table__item" colspan="3">
                                            <label class="attend-form">{{ substr($rest['start_rest'], 0, 5) }}</label>
                                            <label class="approve-middle">~</label>
                                            <label class="attend-form">{{ substr($rest['end_rest'], 0, 5) }}</label>
                                        </td>
                                        <td class="attend-table__item_adjust"></td>                           
                                    </tr>
                                @endforeach
                            @else
                                <p></p>
                            @endif
                            <tr class="attend-table__row">
                                <td class="attend-table__header_adjust"></td>
                                <th class="attend-table__header">備考</th>          
                                <td class="attend-table__item" colspan="3">
                                    <label class="other">{{ $attendance['approveAttendance']['reason'] }}</label>
                                </td>
                                <td class="attend-table__item_adjust"></td>
                            </tr>                              
                        </table>
                    </div>
                    <input type="hidden" name="id" value="{{ $attendance['id'] }}">
                    <button class="correct" type="submit" name="status">承認</button>
                </form>
            @endif
        @endforeach 
    </div>
@endsection