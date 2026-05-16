import socket

DISCOVERY_PORT = 4210
LARAVEL_PORT = 8000
DISCOVERY_MESSAGE = "DISCOVER_LARAVEL"

def get_laptop_ip_for_client(client_ip):
    s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
    try:
        s.connect((client_ip, 1))
        ip = s.getsockname()[0]
    finally:
        s.close()
    return ip

sock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
sock.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
sock.bind(("", DISCOVERY_PORT))

print("Server discovery aktif...")
print(f"Menunggu permintaan ESP di UDP port {DISCOVERY_PORT}")

while True:
    data, addr = sock.recvfrom(1024)
    pesan = data.decode(errors="ignore").strip()

    print(f"Dari {addr}: {pesan}")

    if pesan == DISCOVERY_MESSAGE:
        laptop_ip = get_laptop_ip_for_client(addr[0])
        response = f"LARAVEL_SERVER={laptop_ip}:{LARAVEL_PORT}"
        sock.sendto(response.encode(), addr)
        print(f"Balas ke ESP: {response}")
