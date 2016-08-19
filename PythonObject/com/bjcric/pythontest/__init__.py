from StdSuites.AppleScript_Suite import result

2+2
width=20
height=5*9
print (height+width)
hello="this is a ranther long string containing\nseveral lines of test much as you would do in C"
print(hello);
words=" help dyn"+"+"
print("<"+words*5+">")
print(" pythonz \n python")
print("python")
a = ['spam','eggs',100,1234]
print(a)
print(len(a))
print("feibonaqie")

print("feibonaqie",len(a))
a,b = 0 ,1
while b < 16:
    print (b)
    a,b = b ,a+b

x = int (input("please enter an integer"))
if x < 0:
    x=0
    print("Negative changed to zero")
elif x == 0:
    print ("zero")
elif x == 1:
    print("1")    
    
m = ['cat','window','defenestrate']
for x in m:
    print(x,len(x))
    
for i in range(5,10,3):
    print(i)
    
for i in range(len(m)):
    print(i ,m[i])    
    
def fib(n):
    a,b = 0,1
    while b < n:
        print(b)
        a,b = b, a+b
        
print()
fib(2000)    
     
def fib2(n):
    result=[]
    a,b = 0,1    
    while b < n:
        result.append(b)
        a,b = b,a+b
        
    return result
    
f100 = fib2(100)
print(f100)     
       
     
        
    