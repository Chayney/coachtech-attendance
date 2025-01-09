@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
    <div class="contact-form__content">
        <h1>勤怠詳細</h1>
        @foreach ($attendances as $attendance)
            @if ($attendance->status == '承認待ち')
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
                <p class="red-font">*承認待ちのため修正はできません。</p>
            @else
                <form action="/attendance/update" method="post">
                    @csrf
                    @method('PATCH')
                    <div class="attend-table">
                        <table class="attend-table__inner">                      
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
                                    <input type="text" class="attend-form" name="date_1" value="{{ old('date_1', \Carbon\Carbon::parse($attendance['date'])->isoFormat("YYYY年")) }}">
                                    @error('date_1')
                                        <div class="form__error">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td class="attend-table__item_middle"></td>
                                <td class="attend-table__item">
                                    <input type="text" class="attend-form" name="date_2" value="{{ old('date_2', \Carbon\Carbon::parse($attendance['date'])->isoFormat("M月D日")) }}">
                                    @error('date_2')
                                        <div class="form__error">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td class="attend-table__item_adjust"></td>
                            </tr>
                            <tr class="attend-table__row">
                                <td class="attend-table__header_adjust"></td>
                                <th class="attend-table__header">出勤・退勤</th>          
                                <td class="attend-table__item" colspan="3">
                                    <input type="text" class="attend-form" name="commute" value="{{ old('commute', substr($attendance['commute'], 0, 5)) }}">
                                    <label class="form-middle">~</label>
                                    <input type="text" class="attend-form" name="leave" value="{{ old('leave', substr($attendance['leave'], 0, 5)) }}">
                                    @error('commute')
                                        <div class="form__error">{{ $message }}</div>
                                    @enderror
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
                                            <input type="text" class="attend-form" name="start_rest" value="{{ old('start_rest', substr($rest['start_rest'], 0, 5)) }}">
                                            <label class="form-middle">~</label>
                                            <input type="text" class="attend-form" name="end_rest" value="{{ old('end_rest', substr($rest['end_rest'], 0, 5)) }}">
                                            @if ($errors->has('start_rest'))
                                                <div class="form__error">{{ $errors->first('start_rest') }}</div>
                                            @elseif ($errors->has('end_rest'))
                                                <div class="form__error">{{ $errors->first('end_rest') }}</div>
                                            @endif
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
                                            <input type="text" class="attend-form" name="start_rest" value="{{ old('start_rest', substr($rest['start_rest'], 0, 5)) }}">
                                            <label class="form-middle">~</label>
                                            <input type="text" class="attend-form" name="end_rest" value="{{ old('end_rest', substr($rest['end_rest'], 0, 5)) }}">
                                            @if ($errors->has('start_rest'))
                                                <div class="form__error">{{ $errors->first('start_rest') }}</div>
                                            @elseif ($errors->has('end_rest'))
                                                <div class="form__error">{{ $errors->first('end_rest') }}</div>
                                            @endif
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
                                    <input type="text" class="other" name="reason">
                                    @error('reason')
                                        <div class="form__error">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td class="attend-table__item_adjust"></td>
                            </tr>                              
                        </table>
                    </div>
                    <input type="hidden" name="id" value="{{ $attendance['id'] }}">
                    <input type="hidden" name="attendance_id" value="{{ $attendance['id'] }}">
                    <button class="correct" type="submit" name="status">修正</button>
                </form>
            @endif
        @endforeach 
    </div>
@endsection