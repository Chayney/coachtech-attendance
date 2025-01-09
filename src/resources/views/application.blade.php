@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/application.css') }}">
@endsection

@section('content')
    <div class="contact-form__content">
        <h1 class="page-title">申請一覧</h1>
        <div class="tabs">
            <button class="tab-button active" data-target="tab1">承認待ち</button>
            <button class="tab-button" data-target="tab2">承認済み</button>
        </div>
        <div class="tab-content">
            <div id="tab1" class="tab-pane">
                <div class="attend-table">
                    <table class="attend-table__inner">
                        <tr class="attend-table__row">
                            <th class="attend-table__header">状態</th>
                            <th class="attend-table__header">名前</th>
                            <th class="attend-table__header">対象日時</th>
                            <th class="attend-table__header">申請理由</th>
                            <th class="attend-table__header">申請日時</th>
                            <th class="attend-table__header">詳細</th>
                        </tr>
                        @foreach ($unapproves as $unapprove)
                            <tr class="attend-table__row">                
                                <td class="attend-table__item">
                                    {{ $unapprove['status'] }}        
                                </td>
                                <td class="attend-table__item">
                                    {{ $unapprove['approveUser']['name'] }}
                                </td>
                                <td class="attend-table__item">
                                    {{ \Carbon\Carbon::parse($unapprove['approveAttendance']['date'])->format('Y/m/d') }}
                                </td>
                                <td class="attend-table__item">
                                    {{ $unapprove['approveAttendance']['reason'] }}
                                </td>
                                <td class="attend-table__item">
                                    {{ $unapprove['created_at']->format('Y/m/d') }}
                                </td>
                                <td class="attend-table__item">
                                    <form action="/attendance/{id}" method="get">
                                        <button type="submit" class="detail-btn" name="id" value="{{ $unapprove['id'] }}">
                                            <label>詳細</label>
                                        </button>
                                    </form>
                                </td>              
                            </tr>
                        @endforeach             
                    </table>
                </div>
            </div>
            <div id="tab2" class="tab-pane">
                <div class="attend-table">
                    <table class="attend-table__inner">
                        <tr class="attend-table__row">
                            <th class="attend-table__header">状態</th>
                            <th class="attend-table__header">名前</th>
                            <th class="attend-table__header">対象日時</th>
                            <th class="attend-table__header">申請理由</th>
                            <th class="attend-table__header">申請日時</th>
                            <th class="attend-table__header">詳細</th>
                        </tr>
                        @foreach ($approves as $approve)
                            <tr class="attend-table__row">                
                                <td class="attend-table__item">
                                    {{ $approve['status'] }}
                                </td>
                                <td class="attend-table__item">
                                    {{ $approve['approveUser']['name'] }}
                                </td>
                                <td class="attend-table__item">
                                    {{ \Carbon\Carbon::parse($approve['approveAttendance']['date'])->format('Y/m/d') }}
                                </td>
                                <td class="attend-table__item">
                                    {{ $approve['approveAttendance']['reason'] }}
                                </td>
                                <td class="attend-table__item">
                                    {{ $approve['created_at']->format('Y/m/d') }}
                                </td>
                                <td class="attend-table__item">
                                    <form action="/attendance/{id}" method="get">
                                        <button type="submit" class="detail-btn" name="id" value="{{ $approve['id'] }}">
                                            <label>詳細</label>
                                        </button>
                                    </form>
                                </td>              
                            </tr>
                        @endforeach            
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/application.js') }}" type="text/javascript"></script>
@endsection