import socket
import threading
import json
from datetime import datetime

HOST = "10.129.188.115"
PORT = 22

def validate_payload(data):
    required_fields = ["device_id", "timestamp", "temperature", "courant", "tension"]

    for field in required_fields:
        if field not in data:
            return False, f"Champ manquant: {field}"

    # Vérifications simples
    try:
        datetime.fromisoformat(data["timestamp"].replace("Z", ""))
        float(data["temperature"])
        float(data["courant"])
        float(data["tension"])
    except Exception:
        return False, "Format invalide"

    return True, "OK"


def handle_client(conn, addr):
    print(f"[+] Connexion de {addr}")
    buffer = ""

    try:
        while True:
            data = conn.recv(1024)
            if not data:
                break

            buffer += data.decode()

            # Gestion des messages séparés par \n
            while "\n" in buffer:
                line, buffer = buffer.split("\n", 1)

                try:
                    payload = json.loads(line)

                    valid, message = validate_payload(payload)
                    if not valid:
                        print(f"[!] Donnée invalide de {addr}: {message}")
                        continue

                    print(f"[✓] Donnée reçue de {addr}: {payload}")

                    # 👉 ICI : insertion base de données possible
                    # insert_into_db(payload)

                except json.JSONDecodeError:
                    print(f"[!] JSON invalide de {addr}")

    except Exception as e:
        print(f"[!] Erreur avec {addr}: {e}")

    finally:
        conn.close()
        print(f"[-] Connexion fermée {addr}")


def start_server():
    server = socket.socket(socket.AF_INET, socket.SOCK_STREAM)

    # Permet de redémarrer rapidement le serveur
    server.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)

    server.bind((HOST, PORT))
    server.listen(5)

    print(f"🚀 Serveur TCP en écoute sur {HOST}:{PORT}")

    while True:
        conn, addr = server.accept()

        # Un thread par client
        client_thread = threading.Thread(target=handle_client, args=(conn, addr))
        client_thread.daemon = True
        client_thread.start()


if __name__ == "__main__":
    start_server()