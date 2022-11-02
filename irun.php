<!DOCTYPE html>
<html>
<head>
    <script src="./jspsych.js"></script>
    <script src="./jspsych-html-keyboard-response.js"></script>
    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <link href="./jspsych.css" rel="stylesheet" type="text/css"/>
    <meta charset="utf-8"/>
    <title>Run mix experiment</title>
    <link rel="stylesheet" type="text/css" href="b3.css">
    <link href="my9.css" rel="stylesheet">
</head>
<?php
$name = $_GET['name'] ?? 'Друг';
if (strlen($name) === 0) {
    $name = "Друг";
}

$key = $_GET['key'] ?? rand(10000, 20000);

?>


<body onload="init();" style="background-color:#1b1b1b">
<?php echo $key; ?><a
        href="http://faces.rudych.ru/route.php?key=<?= $key ?>&name=<?= $name ?>&route=111"><font
            size="+2">пойду дальше на опросники</font></a>

<center>
    <canvas id="canvas" width="600" height="800"></canvas>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/howler/2.1.1/howler.min.js"></script>
    <br><br>


    <button style="display: inline-block;" id="message_button1" value=1 class=" action--ls-button-submit btn btn-lg">
        <!--action--ls-button-submit  btn btn-lg btn-primary  -->
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Я&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    </button>
    <button style="display: inline-block;" id="message_button2" value=1 class=" action--ls-button-submit  btn btn-lg ">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?= $name ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    </button>
    <button style="display: inline-block;" id="message_button3" value=1 class=" action--ls-button-submit  btn btn-lg ">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Незнакомец &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    </button>
    <?php //                 <button    style="display: inline-block;" id="message_button4"  value=1  class=" btn   ">двойная картинка                </button> ?>
    <button style="display: inline-block;" id="message_button5" value=1 class=" btn   ">
        пропустить
    </button>


    <script>
        var weich = [2, 2, 2, 2, 2, 2, 2, 2, 2];
        var shufnum = 7;
        var porjadok = [0, 1, 2, 3, 4, 5, 6, 7];
        var wei = [5, 5, 5, 5, 5, 5, 5, 5, 5];
        var imgcount = 0;

        var protorun = Math.floor(Math.random() * 1000) + 1;

        var key = Math.floor(765142);
        var imgmax = Math.floor(150);

        var imgsrc = 'http://morph.rudych.ru/outs/765142/e1n/frame001.png';
        var canvas = document.getElementById('canvas');
        var messageButton1 = document.getElementById('message_button1');
        var messageButton2 = document.getElementById('message_button2');
        var messageButton3 = document.getElementById('message_button3');
        //var messageButton4 = document.getElementById('message_button4');
        var messageButton5 = document.getElementById('message_button5');

        //canvas.width = window.innerWidth*0.9;
        //canvas.height = window.innerHeight*0.85;

        var ctx = canvas.getContext('2d');
        var rect = {};
        var drag = false;
        var imageObj = null;
        var num = 1;
        var pc0 = -1;
        var npc0 = -1;
        var pk0 = -1;
        var pickt = '0000';
        var btnpressed = -1;
        var pktproc = -2;
        var pkt1 = -2;
        var pkt2 = -2;
        var statusbtn = 1;
        var sendb = 1;

        let port, com_writer;

        const btn = document.createElement('button');
        btn.innerHTML = "Connect port";
        btn.addEventListener('click', async () => {
            port = await navigator.serial.requestPort();
            await port.open({
                baudRate: 115200,
                stopBits: 1,
                parity: "none"
            });
            com_writer = port.writable.getWriter();
            await com_writer.write(
                new Uint8Array([109, 112, 50, 0, 0, 0]));

            await com_writer.close()
        });
        document.body.prepend(btn);

        const comsend = async (payload) => {
            const label_codes = [0, 64, 32, 96, 16, 80, 48, 112, 8, 72, 40, 104, 24, 88, 56, 120, 4];
            // console.log('Hello people');
            // // const payload = 125;
            // const params = {
            //     send: {
            //         detail: {
            //             target: 'serial',
            //             action: 'send',
            //             payload: payload.send
            //         }
            //     },
            // };
            // return {
            //     send: new CustomEvent('jspsych', params.send),
            // };

            await com_writer.write(
                new Uint8Array([109, 104, label_codes[+payload.send], 0]));
        };


        document.getElementById('btn').addEventListener('click', () => {
            console.log('scom' + sendb);
            //document.body.
            //var page = document.getElementsByTagName('body')[0];
            //page.classList.add('hidden');
            var sendae = sendb.toString(); //.charCodeAt(0) & 255;
            var sendba = sendae.charCodeAt(0) - 48;

            var myJsPsynchEvents = comsend({
                send: "1"
            });
            // document.dispatchEvent(myJsPsynchEvents.send);
            sendb = sendb + 1;
        });

        function init() {
            imageObj = new Image();
            imageObj.onload = function () {
                ctx.drawImage(imageObj, 0, 0, canvas.width, canvas.height);
                console.log("+");
                //  statusbtn=statusbtn+1;
                //  if (statusbtn==3) {statusbtn=0;}
                var myJsPsynchEvents = comsend({
                    send: "1"
                });
                // document.dispatchEvent(myJsPsynchEvents.send);
                chnames();
            };
            imageObj.onerror = function () {
                //imageObj.src = imgsrc;
                console.log("---------------------------------------------------------------");
                console.log(imgsrc);
                updateImage();
                //imageObj.src = imgsrc;
                //ctx.drawImage(imageObj, 0, 0, canvas.width,canvas.height);
                //statusbtn=4;
                // chnames();
            };
            imageObj.src = imgsrc; // 'outs/765142/e1n/frame001.png';

        }

        function sendtoserv() {
            console.log('sendtoserv');
            console.log(pktproc, pkt1, pkt2);
            var someObj = {
                unit: protorun,
                v: pktproc,
                img: pickt,
                bnt: btnpressed,
                key: key
            };
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'http://morph.rudych.ru/protokey.php');
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.send('param=' + JSON.stringify(someObj));
            xhr.onreadystatechange = function () {
                if (this.readyState == 4) {
                    if (this.status == 200) {
                        console.log(xhr.responseText);
                    } else {
                        console.log('ajax error');
                    }
                }
            };
        }

        function shuffle(array) {
            let currentIndex = array.length,
                randomIndex;

            // While there remain elements to shuffle.
            while (currentIndex != 0) {

                // Pick a remaining element.
                randomIndex = Math.floor(Math.random() * currentIndex);
                currentIndex--;

                // And swap it with the current element.
                [array[currentIndex], array[randomIndex]] = [
                    array[randomIndex], array[currentIndex]
                ];
            }

            return array;
        }

        function updateImage() {
            imgcount = imgcount + 1;
            console.log("imgcount" + imgcount + imgmax);
            if (imgcount > imgmax) {
                imageObj.src = 'fino.png';
                weich = [2, 2, 2, 2, 2, 2, 2, 2, 2];
                shufnum = 7;
                porjadok = [0, 1, 2, 3, 4, 5, 6, 7];
                wei = [5, 5, 5, 5, 5, 5, 5, 5, 5];
                imgcount = 0;


                //let newWin = window.open("about:blank", "hello", "width=200,height=200");

                //newWin.document.write("Вы сделали необходимое количество заданий");
            } else {

                shufnum = shufnum + 1;
                if (shufnum > 7) {
                    var myJsPsynchEvents = comsend({
                        send: "2"
                    });
                    // document.dispatchEvent(myJsPsynchEvents.send);

                    tmpsh = porjadok[shufnum];
                    shufnum = 0;
                    shuffle(porjadok);
                    while (porjadok[0] == tmpsh) {
                        shuffle(porjadok);
                        console.log("!");
                    }
                    console.log(porjadok);
                }
                statusbtn = 1;
                //pktproc=Math.floor(Math.random() * 10)+1 ;

                //pkt1=Math.floor(Math.random() * (8))     ;
                pkt1 = porjadok[shufnum];
                console.log("NNNNNNNNNN " + pkt1);
                pkt2 = 0;
                pkt33 = Math.floor(Math.random() * (3) + 1);
                npkt = ['1n', '2n', '1s', '2s', '1h', '2h', '0x', '0y'];
                pktproc = wei[pkt1];
                if (pkt1 == npc0) {
                    pkt1 = 7;
                }


                if (pkt1 > 6) {
                    pktproc = Math.floor(Math.random() * 10) + 1;
                }

                //if (pktproc==pc0)
                //  {  if (pktproc>8) {pktproc=1;}
                //     else {pktproc=pktproc+2;} } ;
                npc0 = pkt1;
                pc0 = pktproc;
                pk0 = pkt2;
                var s = "000000000" + pktproc;
                console.log(pktproc, pkt1, pkt2, 'outs/765142/e' + npkt[pkt1] + '/frame' + s.substr(s.length - 3) + '.png');
                console.log("neut" + pkt1);
                pickt = npkt[pkt1] + pkt2 + s.substr(s.length - 3);
                //imageObj.onload = function() {console.log("+");};
                imgsrc = 'http://morph.rudych.ru/outs/765142/e' + npkt[pkt1] + '/frame' + s.substr(s.length - 3) + '.png' + '?' + new Date().getTime();
                imageObj.src = imgsrc;
                //            'outs/765142/e'+npkt[pkt1]+'/frame'+    s.substr(s.length-3)+'.png'+'?'+new Date().getTime();
                //imageObj.onload = function() {console.log("+");};
                //  alert(`Изображение загружено, размеры ${imageObj.width}x${imageObj.height}`);

                //imageObj.onerror = function() {
                //console.log("-");
                //  alert("Ошибка во время загрузки изображения");
                //};

            }
        }

        function black() {
            imageObj.src = 'http://morph.rudych.ru/cross.png';
            //canvas.removeAttribute("hidden");
            //messageButton1.style.visibility='visible';
            //messageButton2.style.visibility='visible';
            //messageButton3.style.visibility='visible';
            //messageButton4.style.visibility='visible';
            //messageButton5.style.visibility='visible';

        }

        function updateCross() {
            console.log('cross');
            statusbtn = 0;
            imageObj.src = 'http://morph.rudych.ru/bk.png';
            // var myJsPsynchEvents = comsend({send: "3"});document.dispatchEvent(myJsPsynchEvents.send);

            //var myJsPsynchEvents = comsend({send: "10"});document.dispatchEvent(myJsPsynchEvents.send);
            btnpressed = 0;
            sendtoserv();
            delaytime = Math.floor(Math.random() * (1000));
            console.log("delay" + (delaytime + 10500).toString());
            //var page = document.getElementsByTagName('body')[0];
            //var canvas2 = document.getElementById('canvas');
            //messageButton1.style.visibility='hidden';
            //messageButton2.style.visibility='hidden';
            //messageButton3.style.visibility='hidden';
            //messageButton4.style.visibility='hidden';
            //messageButton5.style.visibility='hidden';
            //canvas.setAttribute("hidden", "hidden");
            setTimeout(black, 1000 + delaytime);
            setTimeout(updateImage, 2000 + delaytime);
        }


        function bindEvent(element, eventName, eventHandler) {
            if (element.addEventListener) {
                element.addEventListener(eventName, eventHandler, false);
            } else if (element.attachEvent) {
                element.attachEvent('on' + eventName, eventHandler);
            }
        }

        function chnames() {
            if (statusbtn == 2) {
                messageButton1.innerHTML = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Нейтральное &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                messageButton2.innerHTML = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Счастье&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                messageButton3.innerHTML = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Печаль&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
            } else if (statusbtn == 1) {
                messageButton1.innerHTML = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;                    Я&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                messageButton2.innerHTML = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;                    тест    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                messageButton3.innerHTML = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;                    Незнакомец    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

            } else if (statusbtn == 0) {
                messageButton1.innerHTML = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                messageButton2.innerHTML = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                messageButton3.innerHTML = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
            } else if (statusbtn == 4) {
                messageButton1.innerHTML = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                messageButton2.innerHTML = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Нажмите дальше  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                messageButton3.innerHTML = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
            }

        }


        //        bindEvent(messageButton4, 'click', function (e) {
        //btnpressed=4;
        //sendtoserv();
        //var myJsPsynchEvents = comsend({send: "3"});
        //document.dispatchEvent(myJsPsynchEvents.send);
        //console.log('б4');
        //updateCross();
        //        });

        bindEvent(messageButton5, 'click', function (e) {
            btnpressed = 5;
            sendtoserv();
            var myJsPsynchEvents = comsend({
                send: "4"
            });
            // document.dispatchEvent(myJsPsynchEvents.send);
            console.log('б5');
            updateCross();
        });

        bindEvent(messageButton1, 'click', function (e) {
            btnpressed = 1;
            var myJsPsynchEvents = comsend({
                send: "5"
            });
            // document.dispatchEvent(myJsPsynchEvents.send);
            sendtoserv();
            if (statusbtn == 2) {
                updateCross();
            }
            if (statusbtn == 1) {
                wei[pkt1] = wei[pkt1] + weich[pkt1];
                if (weich[pkt1] > 1) {
                    weich[pkt1] = weich[pkt1] - 1;
                }
                console.log('+1');
                if (wei[pkt1] > 10) {
                    wei[pkt1] = 6;
                    console.log("shift");
                    weich[pkt1] = 2;
                }
                //  statusbtn=2;
                //  chnames();
                updateCross();
            }

            console.log('<');
        });

        bindEvent(messageButton2, 'click', function (e) {
            btnpressed = 2;
            var myJsPsynchEvents = comsend({
                send: "6"
            });
            // document.dispatchEvent(myJsPsynchEvents.send);

            sendtoserv();
            if (statusbtn == 2) {
                updateCross();
            }
            if (statusbtn == 1) {
                wei[pkt1] = wei[pkt1] - weich[pkt1];
                if (weich[pkt1] > 1) {
                    weich[pkt1] = weich[pkt1] - 1;
                }
                console.log('-1');
                if (wei[pkt1] < 1) {
                    wei[pkt1] = 4;
                    weich[pkt1] = 2;
                }
                //  statusbtn=2;
                //  chnames();
                updateCross();
            }
            console.log('V');
        });

        bindEvent(messageButton3, 'click', function (e) {
            btnpressed = 3;
            var myJsPsynchEvents = comsend({
                send: "7"
            });
            // document.dispatchEvent(myJsPsynchEvents.send);

            sendtoserv();
            if (statusbtn == 2) {
                updateCross();
            }
            if (statusbtn == 1) {
                wei[pkt1] = wei[pkt1] - weich[pkt1];
                console.log('-1 pkt1' + pkt1 + 'wei' + wei[pkt1] + 'weich' + weich[pkt1]);
                if (weich[pkt1] > 1) {
                    weich[pkt1] = weich[pkt1] - 1;
                }

                if (wei[pkt1] < 1) {
                    wei[pkt1] = 4;
                    weich[pkt1] = 2;
                }
                //  statusbtn=2;
                //  chnames();
                updateCross();
            }
            console.log('>');
        });
    </script>


</body>
</html>
