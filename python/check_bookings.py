import mysql.connector

try:
    from python.db import db_config
except ModuleNotFoundError:
    from db import db_config

try:
    from python.facility import FacilityManager
except ImportError:
    from facility import FacilityManager

import asyncio
from datetime import datetime

def get_latest_rents(n=10):
    """
    Get the latest n "Rent" rows from the actividades table.
    
    Args:
        n: Number of rows to retrieve (default: 10)
        
    Returns:
        List of dictionaries containing rent activity data, ordered by date descending
    """
    try:
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor(dictionary=True)
        
        query = """
            SELECT id, contacto, fecha, final, tipo, valor, duracion, cancha, descripcion
            FROM actividades
            WHERE tipo = 'Rent'
            ORDER BY fecha DESC
            LIMIT %s
        """
        
        cursor.execute(query, (n,))
        results = cursor.fetchall()
        
        cursor.close()
        conn.close()
        
        return results
        
    except mysql.connector.Error as err:
        print(f"✗ Database error: {err}")
        return []
    except Exception as e:
        print(f"✗ Error: {e}")
        return []

def is_room_occupied(room=1):
    """
    Check if a room is currently occupied (has a Rent activity where current time is between fecha and final).
    
    Args:
        room: Room/cancha number (default: 1)
        
    Returns:
        True if the room is occupied, False otherwise
    """
    try:
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor()
        
        query = """
            SELECT COUNT(*) as count
            FROM actividades
            WHERE cancha = %s
            AND tipo = 'Rent'
            AND NOW() BETWEEN fecha AND final
        """
        
        cursor.execute(query, (room,))
        result = cursor.fetchone()
        
        cursor.close()
        conn.close()
        
        return result[0] > 0
        
    except mysql.connector.Error as err:
        print(f"✗ Database error: {err}")
        return False
    except Exception as e:
        print(f"✗ Error: {e}")
        return False

async def monitor_and_control_lights(manager, rooms=[1, 2, 3], interval=60):
    """
    Monitor room occupancy and automatically control lights.
    Checks every `interval` seconds and turns on lights for occupied rooms.
    Tracks room state to send OFF commands only once when a room transitions from occupied to available.
    Retries OFF commands until they succeed.
    
    Args:
        manager: FacilityManager instance
        rooms: List of room IDs to monitor (default: [1, 2, 3])
        interval: Check interval in seconds (default: 60 = 1 minute)
    """
    print(f"✓ Starting device discovery...")
    manager.start_discovery()
    
    print(f"✓ Starting lights monitoring every {interval} seconds...")
    
    # Track previous state of each room (True = occupied, False = available)
    room_states = {room: False for room in rooms}
    
    try:
        while True:
            timestamp = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
            
            for room in rooms:
                occupied = is_room_occupied(room)
                previous_state = room_states[room]
                
                # Room is occupied - send ON command every iteration
                if occupied:
                    result = await manager.control_lights(str(room), True)
                    if "error" not in result:
                        if not previous_state:
                            print(f"[{timestamp}] Room {room}: Available → Occupied, Lights ✓ ON")
                        else:
                            print(f"[{timestamp}] Room {room}: Occupied, Lights ON (maintaining)")
                        room_states[room] = True
                    else:
                        print(f"[{timestamp}] Room {room}: ON command failed ({result.get('error')}), will retry...")
                
                # Room became available - send OFF command and retry until success
                elif not occupied and previous_state:
                    result = await manager.control_lights(str(room), False)
                    if "error" not in result:
                        print(f"[{timestamp}] Room {room}: Occupied → Available, Lights ✓ OFF")
                        room_states[room] = False
                    else:
                        print(f"[{timestamp}] Room {room}: OFF command failed ({result.get('error')}), will retry...")
            
            # Wait for the next check interval
            await asyncio.sleep(interval)
            
    except KeyboardInterrupt:
        print("\n✓ Lights monitoring stopped.")
    except Exception as e:
        print(f"✗ Monitoring error: {e}")
    finally:
        manager.stop_discovery()

if __name__ == "__main__":
    # Example: Get the latest 5 rent bookings
    rents = get_latest_rents(5)
    
    if rents:
        print(f"Latest {len(rents)} Rent activities:\n")
        for rent in rents:
            print(f"ID: {rent['id']}")
            print(f"  Contact: {rent['contacto']}")
            print(f"  Date: {rent['fecha']} - {rent['final']}")
            print(f"  Court: {rent['cancha']}, Duration: {rent['duracion']} units")
            print(f"  Value: ${rent['valor']}")
            print(f"  Description: {rent['descripcion']}")
            print()
    else:
        print("No rent activities found")
    
    # Example: Check if room 1 is currently occupied
    print("\nRoom Occupancy Status:")
    for room in [1, 2, 3]:
        occupied = is_room_occupied(room)
        status = "Occupied" if occupied else "Available"
        print(f"  Room {room}: {status}")
    
    # Start lights monitoring
    print("\n" + "="*50)
    manager = FacilityManager()
    
    try:
        asyncio.run(monitor_and_control_lights(manager, rooms=[1, 2, 3], interval=5))
    except KeyboardInterrupt:
        print("Stopped.")
