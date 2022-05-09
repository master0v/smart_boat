#!/usr/bin/python3

from gpiozero import CPUTemperature

def get_cpu_temperature():
  cpu = CPUTemperature()
  return cpu.temperature

if __name__ == '__main__':
    # Running as a script
    print(get_cpu_temperature())