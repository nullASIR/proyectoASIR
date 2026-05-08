import requests
import mysql.connector

# 1. Configuración de tu base de datos (DB-01)
db_config = {
    'host': '127.0.0.1', # O la IP de tu DB-01
    'user': 'root',
    'password': '',
    'database': 'Pimas'
}

def importar_pokemon(cantidad=20):
    try:
        conexion = mysql.connector.connect(**db_config)
        cursor = conexion.cursor()

        print(f"Buscando {cantidad} productos...")

        for i in range(1, cantidad + 1):
            # Obtener datos de PokeAPI
            response = requests.get(f"https://pokeapi.co/api/v2/pokemon/{i}")
            data = response.json()

            nombre = data['name'].capitalize()
            # Esta es la URL de la imagen oficial
            url_imagen = data['sprites']['other']['official-artwork']['front_default']
            precio = 10.50 + i # Precio inventado
            ean = f"840000000{i:03d}" # Generamos un EAN ficticio

            # 2. Insertar en tu tabla con Stock y Tipo
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

importar_pokemon(50) # Importa los primeros 50