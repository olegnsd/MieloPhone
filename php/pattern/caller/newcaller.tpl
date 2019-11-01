[{!MARK!}]
exten => s,1,Answer
exten => s,n,Background({!SOUND!})
exten => s,n,WaitExten(20)
exten => 1,1,goto(up1,s,1)
exten => _x,1,goto(anykey)
exten => s,n,goto(withoutkey,s,1)
exten=>h,1,NoOp(Звонок с номера ${CALLERID(num)} завершен ${STRFTIME(${EPOCH},,%d.%m.%Y-%H:%M:%S)} и длился ${ANSWEREDTIME} секунд.) 
