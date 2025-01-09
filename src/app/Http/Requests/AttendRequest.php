<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'date_1' => 'required|date_format:"Y年"',
            'date_2' => 'required|date_format:"n月j日"',
            'commute' => 'required|date_format:H:i|',
            'leave' => 'required|date_format:H:i',
            'start_rest' => 'required|date_format:H:i',
            'end_rest' => 'required|date_format:H:i',
            'reason' => 'required|string'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $commute = $this->input('commute');
            $leave = $this->input('leave');
            $restIds = $this->input('rest_ids', []);
            $startRests = $this->input('start_rest',[]);
            $endRests = $this->input('end_rest',[]);
            if (strtotime($commute) >= strtotime($leave)) {
                $validator->errors()->add('commute', '出勤時間もしくは退勤時間が不適切な値です');
            }
            foreach ($restIds as $index => $restId) {
                $startRest = $startRests[$index];
                $endRest = $endRests[$index];
                if (strtotime($startRest) < strtotime($commute) || strtotime($startRest) > strtotime($leave)) {
                    $validator->errors()->add('start_rest', '休憩時間が勤務時間外です');
                }
                if (strtotime($endRest) < strtotime($commute) || strtotime($endRest) > strtotime($leave)) {
                    $validator->errors()->add('end_rest', '休憩時間が勤務時間外です');
                }
                if (strtotime($startRest) >= strtotime($endRest)) {
                    $validator->errors()->add('start_rest', '休憩時間が勤務時間外です');
                }
            }
        });
    }

    public function messages()
    {
        return [
            'date_1.required' => '西暦を入力してください。',
            'date_1.date_format' => '西暦はYYYY年形式で入力してください。',
            'date_2.required' => '日付を入力してください。',
            'date_2.date_format' => '日付はM月D日形式で入力してください。',
            'commute.required' => '出勤時間を入力してください。',
            'commute.date_format' => '出勤時間は00:00形式で入力してください。',
            'leave.required' => '退勤時間を入力してください。',
            'leave.date_format' => '退勤時間は00:00形式で入力してください。',
            'start_rest.required' => '休憩開始時間を入力してください。',
            'start_rest.date_format' => '休憩開始時間は00:00形式で入力してください。',
            'end_rest.required' => '休憩終了時間を入力してください。',
            'end_rest.date_format' => '休憩終了時間は00:00形式で入力してください。',
            'reason.required' => '備考を記入してください。',
            'reason.string' => '備考は文字で入力してください'
        ];
    }
}
