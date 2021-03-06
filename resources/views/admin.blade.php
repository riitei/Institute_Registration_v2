<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>後台</title>
    <link rel="stylesheet" type="text/css" href="{{asset('resources/views/index.css')}}">
    <script src="{{asset("lib/jquery-3.2.1.min.js")}}"></script>

    @include("adminjs")
    <script src="{{asset("lib/echarts.js")}}"></script>
</head>
<body>
{{--<style type="text/css">--}}
{{--.department {--}}
{{--color: red;--}}
{{--}--}}

{{--.sum {--}}
{{--color: blue;--}}
{{--}--}}

{{--.school {--}}
{{--background-color: lightgray;--}}
{{--}--}}

{{--</style>--}}
<div class="form">
    <div>
        新增科系：
        <input id="addName" name="addName" type="text" placeholder="新增科系" size="">
        <select id="addDegree" name="addDegree">
            <option value="碩士">碩士</option>
            <option value="博士">博士</option>
        </select>
        <select id="addClass" name="addClass">
            <option value="日">日</option>
            <option value="職">職</option>
        </select>
        <input id="add" type="button" value="新增">
    </div>
    <br>
    <div>
        更新科系：<br>
        選擇更新科系
        <select id="updateDepartment" name="updateDepartment">
            <option value="school_null">請選擇更新科系</option>
            @foreach ($NTCUDepartment as $value)
                <option value="{{$value->department_id}}">{{$value->department_name}}</option>
            @endforeach

        </select><br>
        輸入更新後科系成稱
        <input id="updateName" type="text" placeholder="更新科系">
        <select id="updateDegree" name="updateDegree">
            <option value="degree_null">請選擇學位</option>
            <option value="碩士">碩士</option>
            <option value="博士">博士</option>
        </select>
        <select id="updateClass" name="updateClass">
            <option value="class_null">請選擇班級</option>
            <option value="日">日</option>
            <option value="職">職</option>
        </select>
        <input id="update" type="button" value="更新">
    </div>
    <br>
    <div>
        刪除科系：
        <select id="deleteDepartment" name="deleteDepartment">
            <option value="school_null">請選擇刪除科系</option>
            @foreach ($NTCUDepartment as $value)
                <option value="{{$value->department_id}}">{{$value->department_name}}</option>
            @endforeach
        </select>
        <input id="delete" type="button" value="刪除">
    </div>
    <br><br>
    <div>
        <select id="department" name="department">
            <option value="department_null">請選擇統計報考本校科系</option>
            @foreach ($NTCUDepartment as $value)
                <option value="{{$value->department_id}}">{{$value->department_name}}</option>
            @endforeach
        </select>
        <input id="department_search" type="button" value="查詢">
    </div>
    <div id="gender" style="width: 600px;height:400px;"></div>
    <div id="age" style="width: 600px;height:400px;"></div>
    <div id="school" style="width: 600px;height:2048px;"></div>
    <script>

        $(document).ready(function () {

            $("#department_search").click(function () {
                console.log($("#department").val());
                $.post("Department", {
                        department_id: $("#department").val(),
                        _token: '{{csrf_token()}}'
                    },

                    function (data) {
                    console.log(data);
                        // console.log(JSON.parse(data));
                        var statistics_data = JSON.parse(data);
                        // 報考男女生人數
                        var gender_male = statistics_data['gender']['male'];
                        var gender_female = statistics_data['gender']['female'];
                        // $("#gender").empty();
                        var genderChart = echarts.init(document.getElementById('gender'));
                        var genderOption = {
                            title: {
                                text: '報考男女人數',
                                x: 'center'
                            },
                            tooltip: {
                                trigger: 'item',
                                formatter: "{a} <br/>{b} : {c} 人 ({d}%)"
                            },
                            color: ['#0000ff', '#ff0000'],
                            legend: {
                                orient: 'vertical',
                                left: 'left',
                                data: ["男 " + gender_male + "人", "女 " + gender_female + "人"]
                            },

                            series: [
                                {
                                    name: '報考男女人數',
                                    type: 'pie',
                                    radius: '55%',
                                    center: ['50%', '50%'],
                                    data: [
                                        {value: gender_male, name: "男 " + gender_male + "人"},
                                        {value: gender_female, name: '女 ' + gender_female + "人"},
                                    ]
                                }
                            ]
                        };
                        genderChart.setOption(genderOption);
                        // 報考年紀區間
                        var ageChart = echarts.init(document.getElementById('age'));
                        var age_option = {
                            tooltip: {
                                trigger: 'item',
                                formatter: "{a} <br/>{b}: 共{c}人 ({d}%)"
                            },
                            legend: {
                                orient: 'vertical',
                                x: 'left',
                                data: statistics_data['age_range_name']
                            },
                            series: [
                                {
                                    name: '報考年紀區間',
                                    type: 'pie',
                                    radius: ['50%', '70%'],
                                    avoidLabelOverlap: false,
                                    label: {
                                        normal: {
                                            show: false,
                                            position: 'center'
                                        },
                                        emphasis: {
                                            show: true,
                                            textStyle: {
                                                fontSize: '30',
                                                fontWeight: 'bold'
                                            }
                                        }
                                    },
                                    labelLine: {
                                        normal: {
                                            show: false
                                        }
                                    },
                                    data: statistics_data['age_range']
                                }
                            ]
                        };
                        ageChart.setOption(age_option);
                        // 統計報考人就讀學校科系
                        var schoolChart = echarts.init(document.getElementById('school'));
                        var school_option = {
                            color: ['#3398DB'],
                            tooltip: {
                                trigger: 'axis',
                                axisPointer: {
                                    type: 'shadow'
                                }
                            },
                            grid: {
                                left: '1%',
                                right: '2%',
                                bottom: '1%',
                                containLabel: true
                            },
                            yAxis: [
                                {
                                    type: 'category',
                                    data: statistics_data['school_department'],
                                    axisTick: {
                                        alignWithLabel: true
                                    }
                                }
                            ],
                            xAxis: [
                                {
                                    type: 'value'
                                }
                            ],
                            series: [
                                {
                                    name: '報考人數',
                                    type: 'bar',
                                    barWidth: '20%',
                                    data: statistics_data['school_count']
                                }
                            ]
                        };
                        schoolChart.setOption(school_option);


                    }
                );

            });

        });


    </script>

</div>

</body>
</html>

