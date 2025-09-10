<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BmiSetting;

class BmiSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bmiSettings = [
            [
                'bmi_range_from' => 0,
                'bmi_range_to' => 18.5,
                'classification' => [
                    'en' => 'Underweight',
                    'ar' => 'نقص الوزن'
                ],
                'tips' => [
                    'en' => 'Consider consulting a healthcare provider about healthy weight gain strategies. Focus on nutrient-rich foods and strength training exercises.',
                    'ar' => 'فكر في استشارة مقدم الرعاية الصحية حول استراتيجيات زيادة الوزن الصحي. ركز على الأطعمة الغنية بالعناصر الغذائية وتمارين القوة.'
                ],
                'recommended_water_intake' => [
                    'en' => 'Aim for 8-10 glasses of water daily. Increase intake during physical activities.',
                    'ar' => 'اهدف إلى 8-10 أكواب من الماء يومياً. زد الكمية أثناء الأنشطة البدنية.'
                ],
                'order' => 1,
                'is_active' => true
            ],
            [
                'bmi_range_from' => 18.5,
                'bmi_range_to' => 24.9,
                'classification' => [
                    'en' => 'Normal Weight',
                    'ar' => 'وزن طبيعي'
                ],
                'tips' => [
                    'en' => 'Great job maintaining a healthy weight! Continue with balanced nutrition and regular physical activity.',
                    'ar' => 'عمل رائع في الحفاظ على وزن صحي! استمر في التغذية المتوازنة والنشاط البدني المنتظم.'
                ],
                'recommended_water_intake' => [
                    'en' => 'Maintain 8-10 glasses of water daily. Adjust based on activity level and climate.',
                    'ar' => 'حافظ على 8-10 أكواب من الماء يومياً. اضبط حسب مستوى النشاط والمناخ.'
                ],
                'order' => 2,
                'is_active' => true
            ],
            [
                'bmi_range_from' => 25.0,
                'bmi_range_to' => 29.9,
                'classification' => [
                    'en' => 'Overweight',
                    'ar' => 'زيادة في الوزن'
                ],
                'tips' => [
                    'en' => 'Consider a balanced diet with portion control and increase physical activity. Consult a healthcare provider for personalized guidance.',
                    'ar' => 'فكر في نظام غذائي متوازن مع التحكم في الحصص وزيادة النشاط البدني. استشر مقدم الرعاية الصحية للحصول على إرشادات شخصية.'
                ],
                'recommended_water_intake' => [
                    'en' => 'Increase to 10-12 glasses daily. Water can help with appetite control and metabolism.',
                    'ar' => 'زد إلى 10-12 كوب يومياً. الماء يمكن أن يساعد في التحكم في الشهية والتمثيل الغذائي.'
                ],
                'order' => 3,
                'is_active' => true
            ],
            [
                'bmi_range_from' => 30.0,
                'bmi_range_to' => 34.9,
                'classification' => [
                    'en' => 'Obese Class I',
                    'ar' => 'سمنة درجة أولى'
                ],
                'tips' => [
                    'en' => 'It\'s important to work with healthcare professionals for a comprehensive weight management plan including diet, exercise, and possibly medical intervention.',
                    'ar' => 'من المهم العمل مع المهنيين الصحيين لوضع خطة شاملة لإدارة الوزن تشمل النظام الغذائي والتمارين وربما التدخل الطبي.'
                ],
                'recommended_water_intake' => [
                    'en' => 'Aim for 12-14 glasses daily. Proper hydration supports metabolism and helps reduce hunger.',
                    'ar' => 'اهدف إلى 12-14 كوب يومياً. الترطيب المناسب يدعم التمثيل الغذائي ويساعد في تقليل الجوع.'
                ],
                'order' => 4,
                'is_active' => true
            ],
            [
                'bmi_range_from' => 35.0,
                'bmi_range_to' => 39.9,
                'classification' => [
                    'en' => 'Obese Class II',
                    'ar' => 'سمنة درجة ثانية'
                ],
                'tips' => [
                    'en' => 'Medical supervision is strongly recommended. Consider comprehensive lifestyle changes, nutritional counseling, and discuss treatment options with your doctor.',
                    'ar' => 'الإشراف الطبي موصى به بقوة. فكر في تغييرات شاملة في نمط الحياة، والاستشارة الغذائية، وناقش خيارات العلاج مع طبيبك.'
                ],
                'recommended_water_intake' => [
                    'en' => 'Maintain 14-16 glasses daily. Consult with healthcare provider for personalized hydration needs.',
                    'ar' => 'حافظ على 14-16 كوب يومياً. استشر مقدم الرعاية الصحية لمعرفة احتياجات الترطيب الشخصية.'
                ],
                'order' => 5,
                'is_active' => true
            ],
            [
                'bmi_range_from' => 40.0,
                'bmi_range_to' => 999.0,
                'classification' => [
                    'en' => 'Obese Class III (Severe)',
                    'ar' => 'سمنة درجة ثالثة (شديدة)'
                ],
                'tips' => [
                    'en' => 'Immediate medical attention is recommended. Work closely with healthcare team including doctors, nutritionists, and possibly surgical consultation.',
                    'ar' => 'يُوصى بالعناية الطبية الفورية. اعمل بشكل وثيق مع فريق الرعاية الصحية بما في ذلك الأطباء وأخصائيي التغذية وربما الاستشارة الجراحية.'
                ],
                'recommended_water_intake' => [
                    'en' => 'Follow medical guidance for optimal hydration. Generally 16+ glasses daily with healthcare supervision.',
                    'ar' => 'اتبع التوجيه الطبي للترطيب الأمثل. عموماً 16+ كوب يومياً مع الإشراف الصحي.'
                ],
                'order' => 6,
                'is_active' => true
            ]
        ];

        foreach ($bmiSettings as $setting) {
            BmiSetting::create($setting);
        }
    }
}
