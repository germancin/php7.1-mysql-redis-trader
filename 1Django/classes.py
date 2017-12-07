# class Dogs:

#     def bark(self, str):
#         print("BARK!! " + str)

# mydog = Dogs()
# mydog.bark('dog')

# class Dogs:

#     def __init__(self, name, age):
#         self.name = name
#         self.age = age

#     def bark(self, ages):
#         print("BARK!! {}".format(ages))

# dog = Dogs('firulais', 12)
# print(dog.bark(dog.age))


class Car:
    def __init__(self,year, make, model):
        self.year = year
        self.make = make
        self.model = model
        
    def age(self):
        return 2018 - self.year

carro = Car(1995, 'make', 'model')

carro.age()