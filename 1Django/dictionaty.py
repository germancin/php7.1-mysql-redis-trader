# dogs = {
#     'fido':8,
#     'sally':12,
#     'sean':1
# }

# dogs['marlonsito'] = 8

# print(dogs)


words = ["PoGo","Spange","Lie-Fi"]
definitions = ["Slang for Pokemon Go",
                "To collect spare change, either from couches, passerbys on the street or any numerous other ways and means",
                "When your phone or tablet indicates that you are connected to a wireless network, however you are still unable to load webpages or use any internet services with your device"] 

cont = 0
cooldictionary = {}

for w in words:
    cooldictionary[w] = definitions[cont]
    cont += 1

print(cooldictionary)

