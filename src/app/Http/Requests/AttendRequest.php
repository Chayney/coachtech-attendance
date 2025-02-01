<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

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
        $rules = [
            'date_1' => 'required|date_format:"Y年"',
            'date_2' => 'required|date_format:"n月j日"',
            'commute' => 'required|date_format:H:i',
            'leave' => 'required|date_format:H:i',
            'reason' => 'required'
        ];
        if (is_array($this->start_rest)) {
            $rules['start_rest.*'] = 'nullable|date_format:H:i';
            $rules['end_rest.*'] = 'nullable|date_format:H:i';
        } else {
            $rules['start_rest'] = 'nullable|date_format:H:i';
            $rules['end_rest'] = 'nullable|date_format:H:i';
        }

        return $rules;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->has('commute') && $this->has('leave')) {
                try {
                    $commute = Carbon::createFromFormat('H:i', $this->commute);
                    $leave = Carbon::createFromFormat('H:i', $this->leave);
                } catch (\Exception $e) {
                    $validator->errors()->add('commute', '出勤もしくは退勤時間は00:00形式で入力してください。');
                    return;
                }
                if ($commute >= $leave) {
                    $validator->errors()->add('commute', '出勤時間もしくは退勤時間が不適切な値です。');
                }
                if (is_array($this->start_rest)) {
                    foreach ($this->start_rest as $key => $startRest) {
                        try {
                            $startRestTime = Carbon::createFromFormat('H:i', $startRest);
                            $endRestTime = Carbon::createFromFormat('H:i', $this->end_rest[$key]);
                        } catch (\Exception $e) {
                            $validator->errors()->add("start_rest.{$key}", '休憩時間は00:00形式で入力してください。');
                            return;
                        }
                        if ($startRestTime < $commute || $startRestTime > $leave) {
                            $validator->errors()->add("start_rest.{$key}", '休憩時間が勤務時間外です。');
                        }
                        if ($startRestTime >= $endRestTime) {
                            $validator->errors()->add("start_rest.{$key}", '休憩開始時間が休憩終了時間より後になっています。');
                        }
                    }
                } else {
                    if (!empty($this->start_rest) && !empty($this->end_rest)) {
                        try {
                            $startRest = Carbon::createFromFormat('H:i', $this->start_rest);
                            $endRest = Carbon::createFromFormat('H:i', $this->end_rest);
                        } catch (\Exception $e) {
                            $validator->errors()->add('start_rest', '休憩時間の形式が不正です。');
                            return;
                        }
                        if ($startRest < $commute || $startRest > $leave) {
                            $validator->errors()->add('start_rest', '休憩時間が勤務時間外です。');
                        }
                        if ($startRest >= $endRest) {
                            $validator->errors()->add('start_rest', '休憩開始時間が休憩終了時間より後になっています。');
                        }
                    }
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
            'commute.required' => '出勤もしくは退勤時間を入力してください。',
            'commute.date_format' => '出勤もしくは退勤時間は00:00形式で入力してください。',
            'leave.required' => '出勤もしくは退勤時間を入力してください。',
            'leave.date_format' => '出勤もしくは退勤時間は00:00形式で入力してください。',
            'start_rest.date_format' => '休憩時間は00:00形式で入力してください。',
            'start_rest.*.date_format' => '休憩時間は00:00形式で入力してください。',
            'end_rest.date_format' => '休憩時間は00:00形式で入力してください。',
            'end_rest.*.date_format' => '休憩時間は00:00形式で入力してください。',
            'reason.required' => '備考を記入してください。'
        ];
    }
}
