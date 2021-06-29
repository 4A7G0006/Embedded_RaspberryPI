import RPi.GPIO as GPIO
import requests
import time
import uuid
from bluetooth import *
os.system('sudo hciconfig hci0 piscan')

server_socket=BluetoothSocket(RFCOMM)
server_socket.bind(("",PORT_ANY))
server_socket.listen(1)
port=server_socket.getsockname()[1]
service_id=str(uuid.uuid4())

GPIO.setmode(GPIO.BCM)

TRIG=23
ECHO=24
control_pin=22
GPIO.setup(control_pin,GPIO.OUT)
GPIO.output(control_pin,False)

GPIO.setup(TRIG, GPIO.OUT)
GPIO.setup(ECHO, GPIO.IN)

advertise_service(server_socket, "LEDServer",
                  service_id = service_id,
                  service_classes = [service_id, SERIAL_PORT_CLASS],
                  profiles = [SERIAL_PORT_PROFILE])

def get_distance():

    GPIO.output(TRIG, False)
    time.sleep(0.3)
    GPIO.output(TRIG, True)
    time.sleep(0.00001)
    GPIO.output(TRIG, False)

    while GPIO.input(ECHO)==0:
        start = time.time()

    while GPIO.input(ECHO)==1:
        end = time.time()

    return (end - start) * 17150

try:
    print('按下 Ctrl-C 可停止程式')
    while True:
        print('等待 RFCOMM 頻道 {} 的連線'.format(port))
        client_socket, client_info = server_socket.accept()
        print('接受來自 {} 的連線'.format(client_info))
        try:
            mode_choose=0
            while True:
                print('connect sucessful')
                client_socket.setblocking(True)
                if mode_choose==0:
                    data = client_socket.recv(1024).decode().lower()
                if len(data) == 0:
                    break
                if data == 'mode1' or mode_choose==1 :
                    print('change mode1')
                    client_socket.setblocking(True)
                    while True:
                        data = client_socket.recv(1024).decode().lower()
                        if data =='play':
                            my={'play_status':'5'}
                            requests.get('http://192.168.43.221/FinalTopic/play_status.php',params=my)
                            time.sleep(0.35)
                            my={'play_status':'NULL'}
                            requests.get('http://192.168.43.221/FinalTopic/play_status.php',params=my)
                            print('目前播放中')
                        elif data == 'pause':
                            my={'play_status':'1'}
                            requests.get('http://192.168.43.221/FinalTopic/play_status.php',params=my)
                            time.sleep(0.35)
                            my={'play_status':'NULL'}
                            requests.get('http://192.168.43.221/FinalTopic/play_status.php',params=my)
                            print('目前暫停')
                        elif data=='before':
                            my={'play_status':'3'}
                            requests.get('http://192.168.43.221/FinalTopic/play_status.php',params=my)
                            time.sleep(0.35)
                            my={'play_status':'NULL'}
                            requests.get('http://192.168.43.221/FinalTopic/play_status.php',params=my)
                            print('播放前一首')
                        elif data=='min':
                            my={'play_status':'7'}
                            requests.get('http://192.168.43.221/FinalTopic/play_status.php',params=my)
                            time.sleep(0.35)
                            my={'play_status':'NULL'}
                            requests.get('http://192.168.43.221/FinalTopic/play_status.php',params=my)
                            print('音量降低')
                        elif data=='stop':
                            my={'play_status':'2'}
                            requests.get('http://192.168.43.221/FinalTopic/play_status.php',params=my)
                            time.sleep(0.35)
                            my={'play_status':'NULL'}
                            requests.get('http://192.168.43.221/FinalTopic/play_status.php',params=my)
                            print('停止歌曲')
                        elif data=='plus':
                            my={'play_status':'6'}
                            requests.get('http://192.168.43.221/FinalTopic/play_status.php',params=my)
                            time.sleep(0.35)
                            my={'play_status':'NULL'}
                            requests.get('http://192.168.43.221/FinalTopic/play_status.php',params=my)
                            print('音量增加')
                        elif data=='next':
                            my={'play_status':'4'}
                            requests.get('http://192.168.43.221/FinalTopic/play_status.php',params=my)
                            time.sleep(0.35)
                            my={'play_status':'NULL'}
                            requests.get('http://192.168.43.221/FinalTopic/play_status.php',params=my)
                            print('播放下一首')
                        elif data=='mode2':
                            mode_choose=2
                            break
                if data=='mode2' or mode_choose==2:
                    time.sleep(0.5)
                    client_socket.setblocking(False)
                    print('change mode2')
                    n=False
                    nn=False
                    while True:
                        if get_distance() < 6 and n==False:
                            GPIO.output(control_pin,True)
                            my={'play_status':'5'}
                            requests.get('http://192.168.43.221/FinalTopic/play_status.php',params=my)
                            time.sleep(0.4)
                            my={'play_status':'NULL'}
                            requests.get('http://192.168.43.221/FinalTopic/play_status.php',params=my)
                            print('播放')
                            nn=False
                            n=True
                        elif get_distance() >= 6 and nn==False:
                            GPIO.output(control_pin,False)
                            my={'play_status':'1'}
                            requests.get('http://192.168.43.221/FinalTopic/play_status.php',params=my)
                            time.sleep(0.4)
                            my={'play_status':'NULL'}
                            requests.get('http://192.168.43.221/FinalTopic/play_status.php',params=my)
                            print('暫停')
                            n=False
                            nn=True
                        try:
                            data=client_socket.recv(1024).decode().lower()
                            if data=='mode1':
                                mode_choose=1
                            break
                        except IOError:
                           continue
#                else:
#                    print('未知的指令: {}'.format(data))
        except IOError:
            mode_choose=1
            continue
except KeyboardInterrupt:
    print('中斷程式')
finally:
    if 'client_socket' in vars():
        client_socket.close()
    server_socket.close()
    GPIO.cleanup()
    print('中斷連線')


