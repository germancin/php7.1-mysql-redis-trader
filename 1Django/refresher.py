age = 22
name = 'German'
print("My name is {} and I am {}" .format(name, age))


if age > 18:
    print("{} is older than 18 \n\n\n".format(name))
elif age < 18 and age > 16:
    print("{} is younger than 18 and older than 16 he can drive \n\n\n".format(name))
else:
    print("{} is too young to do anything \n\n".format(name))