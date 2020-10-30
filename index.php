<?php

	
date_default_timezone_set('Asia/Ho_Chi_Minh');
function getThoiTiet($region, $access_key) {
    $location = $region;
    $array_json = "http://api.openweathermap.org/data/2.5/weather?q=" . $location . $access_key;
    $json = file_get_contents($array_json);
    $obj = json_decode($json);
    return $obj;
}

function noiChuoi($name){
    $inp = explode(' ',$name);
    $count = count($inp);
    for($i=0; $i<$count; $i++){
    $outp = implode("%20", $inp);
    }
    return $outp;
}
function getDuBao5ngay($region, $access_key) {
    $location = $region;
    $array_json = "http://api.openweathermap.org/data/2.5/forecast?q=" . $location . $access_key;
    $json = file_get_contents($array_json);
    $obj = json_decode($json);
    return $obj;
}

function getTranDau($name){
    $uri1 = 'https://api.football-data.org/v2/competitions/'.$name.'/matches/?status=SCHEDULED';
    $reqPrefs['http']['method'] = 'GET';
    $reqPrefs['http']['header'] = 'X-Auth-Token: 4e382a3af3dc4972b81c3d41b15bc6e8';
    $stream_context1 = stream_context_create($reqPrefs);
    $json = file_get_contents($uri1, false, $stream_context1);
    $obj = json_decode($json);
    return $obj;
}
function getXepHang($name){
    $uri1 = 'http://api.football-data.org/v2/competitions/'.$name.'/standings';
    $reqPrefs['http']['method'] = 'GET';
    $reqPrefs['http']['header'] = 'X-Auth-Token: 4e382a3af3dc4972b81c3d41b15bc6e8';
    $stream_context1 = stream_context_create($reqPrefs);
    $json = file_get_contents($uri1, false, $stream_context1);
    $obj = json_decode($json);
    return $obj;
}


function layanh($long){
    $url='https://api.serpwow.com/live/search?api_key=E5A2BFF0DD2E4AA88A55A56393860F85&q='.$long;
    $array_json=file_get_contents($url);
    $json=json_decode($array_json);
    $img=$json->knowledge_graph->images[0];
    return $img;
}

//Require tập tin autoload.php
require './vendor/autoload.php';

//Khai báo sử dụng thư viện
use App\Http\Controllers\BotManController;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Drivers\Events\GenericEvent;
use BotMan\Drivers\Facebook\Extensions\GenericTemplate;
use BotMan\Drivers\Facebook\Extensions\Element;
use BotMan\Drivers\Facebook\Extensions\ElementButton;
use BotMan\Drivers\Facebook\Extensions\ButtonTemplate;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Attachments\Audio;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;



//Khai báo cấu hình chat bot
$config = [
    'facebook' => [   
      'token' => 'EAAMhdtpKYHQBADkMVJV6AqYuT8m77ZC0GpUxDhmxjXzOZBBePanDdiIZCT3F7TC5md8cPevYJgYLR0ZCSmrQ7OTx2AsZB5j8dW7TecPdQu3wdnZCO81DisZC79CpyakE4jOVobKblpoUtMmhpqGAr9Rd2PIDQZBcyZCzP12UHPUmVoAZDZD',
      'app_secret' => '2b90d4b9ba452376bd49dc5027eb5e73',
      'verification'=>'demo',
  ]
  
];

//Nạp driver để sử dụng
DriverManager::loadDriver(\BotMan\Drivers\Facebook\FacebookDriver::class);

//Khởi tạo instance
$botman = BotManFactory::create($config);






//Dạy chat bot
$botman->hears('Hi', function(BotMan $bot) {
    $bot->reply(Question::create('Chào mừng bạn đến với bot man, mời bạn chọn các chức năng tra cứu')->addButtons([
        Button::create('1. Thời tiết')->value('Tra cứu thông tin thời tiết'),
        Button::create('2. Bóng đá')->value('Tra cứu thông tin bóng đá'),
        Button::create('3. nghe nhạc')->value('nghe nhạc'),
        
    ]));
});

//I. Tra cứu thông tin thời tiết
$botman->hears('Tra cứu thông tin thời tiết', function (BotMan $bot) {
    $bot->reply(ButtonTemplate::create('Mời bạn chọn: ')
    
    ->addButton(ElementButton::create('thời tiết theo địa điểm')
        ->type('postback')
        ->payload('địa điểm')
    )
    ->addButton(ElementButton::create('Thời tiết tại vị trí')
        ->type('postback')
        ->payload('Vị trí')
    )
);
  
});
$botman->hears('địa điểm', function (BotMan $bot) {
    $bot->reply(ButtonTemplate::create('Mời bạn chọn: ')
    
    ->addButton(ElementButton::create('thời tiết tại địa điểm')
        ->type('postback')
        ->payload('thời tiết hôm nay')
    )
    ->addButton(ElementButton::create('dự báo tại địa điểm')
        ->type('postback')
        ->payload('dự báo')
    )
);
  
});

//II. Tra cứu thông tin bóng đá
$botman->hears('Tra cứu thông tin bóng đá', function (BotMan $bot) {
    $bot->reply(ButtonTemplate::create('Mời bạn chọn: ')
    ->addButton(ElementButton::create('Bảng xếp hạng')
        ->type('postback')
        ->payload('Xếp hạng')
    )
    ->addButton(ElementButton::create('Lịch thi đấu')
        ->type('postback')
        ->payload('Lịch đấu')
    )
    ->addButton(ElementButton::create('top 10 ghi bàn')
    ->type('postback')
    ->payload('danh sách ghi bàn')
)
);
  
});

//2.1 Bảng xếp hạng
$botman->hears('Xếp hạng', function(BotMan $bot) {
    $bot->reply(Question::create('Chọn giải đấu')->addButtons([
        Button::create('Premier League')->value('Mã giải 2021'),
        
    ]));
});


$botman->hears('Mã giải {name}', function ($bot, $name) {
    $current_obj = getXepHang($name);
    $bot->reply(
        GenericTemplate::create()
            ->addImageAspectRatio(GenericTemplate::RATIO_HORIZONTAL)
           
            ->addElements([
                    Element::create('Mời chọn đội: ') 
                    ->addButton(ElementButton::create($current_obj->standings[0]->table[0]->team->name)
                    ->payload('Chọn đội 0 giải '.$current_obj->competition->id)
                    ->type('postback')
                    )
                    ->addButton(ElementButton::create($current_obj->standings[0]->table[1]->team->name)
                    ->payload('Chọn đội 1 giải '.$current_obj->competition->id)
                    ->type('postback')
                    )
                    ->addButton(ElementButton::create($current_obj->standings[0]->table[2]->team->name)
                    ->payload('Chọn đội 2 giải '.$current_obj->competition->id)
                    ->type('postback')
                    ),
                    Element::create('Mời chọn đội: ') 
                    ->addButton(ElementButton::create($current_obj->standings[0]->table[3]->team->name)
                    ->payload('Chọn đội 3 giải '.$current_obj->competition->id)
                    ->type('postback')
                    )
                    ->addButton(ElementButton::create($current_obj->standings[0]->table[4]->team->name)
                    ->payload('Chọn đội 4 giải '.$current_obj->competition->id)
                    ->type('postback')
                    )
                    ->addButton(ElementButton::create($current_obj->standings[0]->table[5]->team->name)
                    ->payload('Chọn đội 5 giải '.$current_obj->competition->id)
                    ->type('postback')
                    ),
                    Element::create('Mời chọn đội: ') 
                    ->addButton(ElementButton::create($current_obj->standings[0]->table[6]->team->name)
                    ->payload('Chọn đội 6 giải '.$current_obj->competition->id)
                    ->type('postback')
                    )
                    ->addButton(ElementButton::create($current_obj->standings[0]->table[7]->team->name)
                    ->payload('Chọn đội 7 giải '.$current_obj->competition->id)
                    ->type('postback')
                    )
                    ->addButton(ElementButton::create($current_obj->standings[0]->table[8]->team->name)
                    ->payload('Chọn đội 8 giải '.$current_obj->competition->id)
                    ->type('postback')
                    ),
                    Element::create('Mời chọn đội: ') 
                    ->addButton(ElementButton::create($current_obj->standings[0]->table[9]->team->name)
                    ->payload('Chọn đội 9 giải '.$current_obj->competition->ide)
                    ->type('postback')
                    )
                    ->addButton(ElementButton::create($current_obj->standings[0]->table[10]->team->name)
                    ->payload('Chọn đội 10 giải '.$current_obj->competition->id)
                    ->type('postback')
                    )
                    ->addButton(ElementButton::create($current_obj->standings[0]->table[11]->team->name)
                    ->payload('Chọn đội 11 giải '.$current_obj->competition->id)
                    ->type('postback')
                    ),
                    Element::create('Mời chọn đội: ') 
                    ->addButton(ElementButton::create($current_obj->standings[0]->table[12]->team->name)
                    ->payload('Chọn đội 12 giải '.$current_obj->competition->id)
                    ->type('postback')
                    )
                    ->addButton(ElementButton::create($current_obj->standings[0]->table[13]->team->name)
                    ->payload('Chọn đội 13 giải '.$current_obj->competition->id)
                    ->type('postback')
                    )
                    ->addButton(ElementButton::create($current_obj->standings[0]->table[14]->team->name)
                    ->payload('Chọn đội 14 giải '.$current_obj->competition->id)
                    ->type('postback')
                    ),
                    Element::create('Mời chọn đội: ') 
                    ->addButton(ElementButton::create($current_obj->standings[0]->table[15]->team->name)
                    ->payload('Chọn đội 15 giải '.$current_obj->competition->id)
                    ->type('postback')
                    )
                    ->addButton(ElementButton::create($current_obj->standings[0]->table[16]->team->name)
                    ->payload('Chọn đội 16 giải '.$current_obj->competition->id)
                    ->type('postback')
                    )
                    ->addButton(ElementButton::create($current_obj->standings[0]->table[17]->team->name)
                    ->payload('Chọn đội 17 giải '.$current_obj->competition->id)
                    ->type('postback')
                    ),
                    Element::create('Mời chọn đội: ') 
                    ->addButton(ElementButton::create($current_obj->standings[0]->table[18]->team->name)
                    ->payload('Chọn đội 18 giải '.$current_obj->competition->id)
                    ->type('postback')
                    )
                    ->addButton(ElementButton::create($current_obj->standings[0]->table[19]->team->name)
                    ->payload('Chọn đội 19 giải '.$current_obj->competition->id)
                    ->type('postback')
                    ),
            ])
    );
});

$botman->hears('Chọn đội {num} giải {name}', function ($bot, $num, $name) {
    $current_obj = getXepHang($name);
    $bot->reply(' Xếp hạng: '.$current_obj->standings[0]->table[$num]->position);
    $bot->reply('Thắng '.$current_obj->standings[0]->table[$num]->won.' Hòa '.$current_obj->standings[0]->table[$num]->draw.' Thua '.$current_obj->standings[0]->table[$num]->lost);
    $bot->reply('Điểm số: '.$current_obj->standings[0]->table[$num]->points);
    $bot->reply(Question::create('bạn có muốn tiếp tục không')->addButtons([
        Button::create('có')->value('có'),
        Button::create('không')->value('không'),
        
    ]));
});



//2.2 Lịch thi đấu
$botman->hears('Lịch đấu', function ($bot) {
    $bot->reply(Question::create('Bạn muốn xem lịch thi đấu giải nào')->addButtons([
        Button::create('UEFA Champions League')->value('Giải đấu CL'),
        Button::create('Ngoại hạng Anh')->value('Giải đấu PL'),
        
    ]));
});

$botman->hears('Giải đấu {name}', function ($bot, $name) {
    $bot->typesAndWaits(2);
    $b41=0;
    $b45=getTranDau($name);
    $b40=$b45->matches;
    $img = layanh($b45->competition->code);
      for($i=0;$i<11;$i++){
        $ss=explode("T",$b40[$i]->utcDate);
        if(strtotime(date('Y-m-d'))==strtotime($ss[0])){
            $ss2=explode("Z",$ss[1]);
             $time=$ss[0].' '.$ss2[0];
            $time_stamp = strtotime($time);
            $new = $time_stamp + 7*60*60;
           $gio[$i]=date('d-m-Y H:i:s',$new);
            $a[$i]=$b40[$i]->awayTeam->name;
            $h[$i]=$b40[$i]->homeTeam->name;
       $b1= Element::create($gio[$i])
       ->image($img)
       ->addButton(ElementButton::create($a[$i])
       ->url('http://bongdaso.com/news.aspx')
       ) 
       ->addButton(ElementButton::create($h[$i])
       ->payload('1')
       ->type('postback')
        );
        $b3[]=$b1;
        $b41=1;
         }
        }
        if($b41==0){
        $bot->reply('hôm nay không có trận đấu nào cả');
        }
        else{
             $bot->reply(  GenericTemplate::create()
            ->addImageAspectRatio(GenericTemplate::RATIO_HORIZONTAL)
            ->addElements($b3)
        );
          }
        
    $bot->reply(Question::create('bạn có muốn tiếp tục không')->addButtons([
        Button::create('có')->value('có'),
        Button::create('không')->value('không'),
        
    ]));
  
         
            
       
       });

//danh sach ghi ban
$botman->hears('danh sách ghi bàn', function ($bot) {
    $bot->reply(Question::create('Bạn muốn xem danh sách ghi bàn đấu giải nào')->addButtons([
        Button::create('UEFA Champions League')->value('ghi bàn CL'),
        Button::create('Ngoại hạng Anh')->value('ghi bàn PL'),
        
    ]));
});
$botman->hears('ghi bàn {name}', function ($bot, $name) {
    $bot->typesAndWaits(2);
    $uri = 'https://api.football-data.org/v2/competitions/'.$name.'/scorers/?limit=10';
    $reqPrefs['http']['method'] = 'GET';
    $reqPrefs['http']['header'] = 'X-Auth-Token: 4e382a3af3dc4972b81c3d41b15bc6e8';
    $stream_context = stream_context_create($reqPrefs);
    $response = file_get_contents($uri, false, $stream_context);
    $matches = json_decode($response);
    $b0=$matches->scorers;
    $img = layanh($matches->competition->code);
    for($i=0;$i<10;$i++){
        
             $b1[]=Element::create($b0[$i]->player->name)
                ->image($img)
             ->addButton(ElementButton::create($b0[$i]->numberOfGoals."bàn")
            ->url('http://bongdaso.com/news.aspx')
            ) 
            ->addButton(ElementButton::create('xem thên tin bóng đá')
            ->url('http://bongdaso.com/news.aspx')
         );
    }
      $bot->reply(
         GenericTemplate::create()
        ->addImageAspectRatio(GenericTemplate::RATIO_HORIZONTAL)
       ->addElements($b1)
    );
    $bot->reply(Question::create('bạn có muốn tiếp tục không')->addButtons([
        Button::create('có')->value('có'),
        Button::create('không')->value('không'),
        
    ]));

    });
    $botman->hears('có', function(BotMan $bot) {
        $bot->reply(Question::create('chọn chức năng')->addButtons([
            Button::create('1. Thời tiết')->value('Tra cứu thông tin thời tiết'),
            Button::create('2. Bóng đá')->value('Tra cứu thông tin bóng đá'),
            Button::create('3. nghe nhạc')->value('nghe nhạc'),
            
        ]));
    });
    $botman->hears('không', function(BotMan $bot) {
        $bot->reply('bye');
    });
//1.1 Dự báo thời tiết
$botman->hears('Dự báo', function ($bot) {
    $bot->reply('Bạn muốn dự báo thời tiết tỉnh nào');
});

$botman->hears('Dự báo thời tiết {name}', function ($bot, $name) {
    $access_key = "&appid=5931bcb78d69df150224be6e0b206ee2"; 
    $region = $name;
    $current_obj = getDuBao5ngay($region, $access_key);
    
    for ($i=0; $i < 40; $i+=8) { 
        $t1=$current_obj->list[$i]->main->temp_min-273;
        $t2=$current_obj->list[$i]->main->temp_max-273;
        $b1[] = Element::create($current_obj->list[$i]->dt_txt)
                ->image('http://openweathermap.org/img/w/'.$current_obj->list[$i]->weather[0]->icon.'.png') 
                ->addButton(ElementButton::create($current_obj->city->name.": ".$current_obj->list[$i]->weather[0]->main)
                ->url('http://openweathermap.org/img/w/')
                ) 
                ->addButton(ElementButton::create("Temp min: ".$t1."°C")
                ->payload('1')
                ->type('postback')
                )
                ->addButton(ElementButton::create("Temp max: ".$t2."°C")
                ->payload('2')
                ->type('postback')
                );
    }
        $bot->reply(
           
            GenericTemplate::create()
        ->addImageAspectRatio(GenericTemplate::RATIO_HORIZONTAL)
       
        ->addElements($b1)
    );
    $bot->reply(Question::create('bạn có muốn tiếp tục không')->addButtons([
        Button::create('có')->value('có'),
        Button::create('không')->value('không'),
        
    ]));
});
//1.2 Thời tiết hôm nay
$botman->hears('Thời tiết hôm nay', function ($bot) {
    $bot->reply('Bạn muốn biết thời tiết của tỉnh nào:');
});
$botman->hears('Thời tiết {name}', function ($bot, $name) {
        $access_key = "&appid=5931bcb78d69df150224be6e0b206ee2"; 
        $region = $name;
        $current_obj = getThoiTiet($region, $access_key);
        $bot->reply($current_obj->name);
        $attachment = new Image('http://openweathermap.org/img/w/'.$current_obj->weather[0]->icon.'.png');
        $message = OutgoingMessage::create('This is my text')
                ->withAttachment($attachment);
        $bot->reply($message);
        $temp = $current_obj->main->temp-273;
        switch ($current_obj->weather[0]->description) {
            case 'few clouds':
                $bot->reply('Ít mây');
                break;
            case 'haze':
                $bot->reply('Có sương mù');
                break;
            case 'overcast clouds':
                $bot->reply('Nhiều mây');
                break;
            case 'scattered clouds':
                $bot->reply('Mây rải rác');
                break;
            case 'light rain':
                $bot->reply('Mưa nhỏ');
                break;
            case 'mist':
                $bot->reply('Sương mù nhẹ');
                break;
            default:
                $bot->reply($current_obj->weather[0]->description);
                break;
        }
        $bot->reply('Nhiệt độ: '.$temp);
        $bot->reply(Question::create('bạn có muốn tiếp tục không')->addButtons([
            Button::create('có')->value('có'),
            Button::create('không')->value('không'),
            
        ]));
});
$botman->hears('Vị trí', function ($bot) {
    $bot->reply(Question::create('Chọn chức năng')->addButtons([
        Button::create('Thời tiết hiện tại')->value('currentWeather'),
        Button::create('Dự báo thời tiết')->value('forecastWeather'),
        
    ]));
});

$botman->hears('currentWeather', function ($bot) {
    $access_key = "&appid=5931bcb78d69df150224be6e0b206ee2";
    $array_json = "http://api.ipstack.com/check?access_key=10f4481bb657b14b942c38b342ed3716";
    $json = file_get_contents($array_json);
    $obj = json_decode($json);
    $current_obj = getThoiTiet($obj->region_name, $access_key);
    $bot->reply($obj->region_name);
    $attachment = new Image('http://openweathermap.org/img/w/'.$current_obj->weather[0]->icon.'.png');
        $message = OutgoingMessage::create('This is my text')
                ->withAttachment($attachment);
        $bot->reply($message);
        $temp = $current_obj->main->temp-273;
        switch ($current_obj->weather[0]->description) {
            case 'few clouds':
                $bot->reply('Ít mây');
                break;
            case 'haze':
                $bot->reply('Có sương mù');
                break;
            case 'overcast clouds':
                $bot->reply('Nhiều mây');
                break;
            case 'scattered clouds':
                $bot->reply('Mây rải rác');
                break;
            case 'light rain':
                $bot->reply('Mưa nhỏ');
                break;
            case 'mist':
                $bot->reply('Sương mù nhẹ');
                break;
            case 'broken clouds':
                $bot->reply('Nhiều mây');
                break;
            default:
                $bot->reply($current_obj->weather[0]->description);
                break;
        }
        $bot->reply('Nhiệt độ: '.$temp);
        $bot->reply(Question::create('bạn có muốn tiếp tục không')->addButtons([
            Button::create('có')->value('có'),
            Button::create('không')->value('không'),
            
        ]));
});
$botman->hears('forecastWeather', function ($bot) {
    $access_key = "&appid=5931bcb78d69df150224be6e0b206ee2";
    $array_json = "http://api.ipstack.com/check?access_key=10f4481bb657b14b942c38b342ed3716";
    $json = file_get_contents($array_json);
    $obj = json_decode($json);
    $current_obj = getDuBao5ngay($obj->region_name, $access_key);
    for ($i=0; $i < 40; $i+=8) { 
            $t1=$current_obj->list[$i]->main->temp_min-273;
            $t2=$current_obj->list[$i]->main->temp_max-273;
            $b1[] = Element::create($current_obj->list[$i]->dt_txt)
                    ->image('http://openweathermap.org/img/w/'.$current_obj->list[$i]->weather[0]->icon.'.png') 
                    ->addButton(ElementButton::create($current_obj->city->name.": ".$current_obj->list[$i]->weather[0]->main)
                    ->url('http://bongdaso.com/news.aspx')
                    ) 
                    ->addButton(ElementButton::create("Temp min: ".$t1."°C")
                    ->payload('1')
                    ->type('postback')
                    )
                    ->addButton(ElementButton::create("Temp max: ".$t2."°C")
                    ->payload('2')
                    ->type('postback')
                    );
        }
            $bot->reply(
               
                GenericTemplate::create()
            ->addImageAspectRatio(GenericTemplate::RATIO_HORIZONTAL)
           
            ->addElements($b1)
        );
        $bot->reply(Question::create('bạn có muốn tiếp tục không')->addButtons([
            Button::create('có')->value('có'),
            Button::create('không')->value('không'),
            
        ]));
});

$botman->hears('nghe nhạc', function ($bot) {
    $bot->reply('Bạn muốn nghe bài hát nào');
});
$botman->hears('bài hát {name}', function ($bot, $name) {
    $bot->typesAndWaits(2);
    $baiHat = noiChuoi($name);
    $curl = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => "https://deezerdevs-deezer.p.rapidapi.com/search?q=".$baiHat,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
        "x-rapidapi-host: deezerdevs-deezer.p.rapidapi.com",
        "x-rapidapi-key: 0540864c26mshe95dfea26159ad6p1bf3a1jsn40d95df34cc4"
    ),
));
$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);
$obj = json_decode($response);

$attachment = new Audio($obj->data[0]->preview, [
    'custom_payload' => true,
]);
$message = OutgoingMessage::create('This is my text')
            ->withAttachment($attachment);
            for($i=0; $i<10; $i++){
        $b1[]= Element::create($obj->data[$i]->title."-".$obj->data[$i]->artist->name)
                    ->image($obj->data[$i]->artist->picture_medium)
                    ->addButton(ElementButton::create("Nghe nhạc")
                    ->url($obj->data[$i]->link)
                    )
                    ->addButton(ElementButton::create("Xem album")
                    ->payload('Xem album '.$obj->data[$i]->album->id)
                    ->type('postback')
                    )
                   
                    ;
                }
$bot->reply(
        GenericTemplate::create()
            ->addImageAspectRatio(GenericTemplate::RATIO_HORIZONTAL)
           
            ->addElements(
                    $b1
               )
    );
    $attachment = new Audio($obj->data[0]->preview, [
        'custom_payload' => true,
    ]);
    $message = OutgoingMessage::create('This is my text')
    ->withAttachment($attachment);

// Reply message object
$bot->reply($message);
$bot->reply(Question::create('bạn có muốn tiếp tục không')->addButtons([
    Button::create('có')->value('có'),
    Button::create('không')->value('không'),
    
]));
});




$botman->hears('Xem album {name}', function ($bot, $name) {
    $curl = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => "https://deezerdevs-deezer.p.rapidapi.com/album/".$name,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
        "x-rapidapi-host: deezerdevs-deezer.p.rapidapi.com",
        "x-rapidapi-key: 0540864c26mshe95dfea26159ad6p1bf3a1jsn40d95df34cc4"
    ),
));
$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);
$obj = json_decode($response);
for($i=0; $i<10; $i++){
        $b1[]= Element::create("Album: ".$obj->title)
                    ->image($obj->cover_medium)
                    ->addButton(ElementButton::create($obj->tracks->data[$i]->title)
                    ->url($obj->tracks->data[$i]->link)
                    )
                    ->addButton(ElementButton::create("Nghe nhạc")
                    ->url($obj->tracks->data[$i]->link)
                    );
                }
$bot->reply(
        GenericTemplate::create()
            ->addImageAspectRatio(GenericTemplate::RATIO_HORIZONTAL)
           
            ->addElements(
                    $b1
               )
    );
    $bot->reply(Question::create('bạn có muốn tiếp tục không')->addButtons([
        Button::create('có')->value('có'),
        Button::create('không')->value('không'),
        
    ]));
});



//Bắt đầu lắng nghe
$botman->listen();  