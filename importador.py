import requests
import mysql.connector

db_config = {
    'host': '192.168.10.10', # O la IP de tu DB-01
    'user': 'adminRemoto',
    'password': 'Abc1234',
    'database': 'Pimas'
}

def importar_pokemon(cantidad=20):
    try:
        conexion = mysql.connector.connect(**db_config)
        cursor = conexion.cursor()

        print(f"Buscando {cantidad} productos...")

        for i in range(1, cantidad + 1):
            response = requests.get(f"https://pokeapi.co/api/v2/pokemon/{i}")
            data = response.json()

            nombre = data['name'].capitalize()
            url_imagen = data['sprites']['other']['official-artwork']['front_default']
            precio = 10.50 + i 
            ean = f"840000000{i:03d}"

            query = "INSERT INTO Productos (Ean, Nombre, Precio, Imagen, Stock, Tipo, Estado) VALUES (%s, %s, %s, %s, %s, %s, %s)"
            cursor.execute(query, (ean, nombre, precio, url_imagen, 10, 'Pokémon', 'Nuevo'))

            print(f"Importado: {nombre}")

        conexion.commit()
        print("¡Éxito! Base de datos actualizada.")

    except Exception as e:
        print(f"Error: {e}")
    finally:
        if conexion.is_connected():
            cursor.close()
            conexion.close()

importar_pokemon(1302)