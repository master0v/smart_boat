#!/usr/bin/python3

import asyncio
from pyppeteer import launch # https://miyakogi.github.io/pyppeteer/reference.html

page_url = 'http://localhost:3001/d/ut-lBLmRk/main?orgId=1&kiosk'

async def main():
  
  # chromium-browser --no-sandbox --disable-gpu --disable-timeouts-for-profiling --disable-low-res-tiling "http://localhost:3001/d/ut-lBLmRk/main?orgId=1&kiosk"
  # --enable-low-res-tiling 
  browser = await launch(
    args=["--disable-gpu", "--no-sandbox", "--disable-low-res-tiling", "--disable-timeouts-for-profiling"],
    executablePath="/usr/bin/chromium-browser",
    headless=True) # ( {'headless': True} );
  page = await browser.newPage()
  await page.setViewport({'width': 1200, 'height': 880})
  await page.goto(page_url, {'waitUntil': 'networkidle2', 'timeout': '60000'})
  await page.screenshot({'path': 'solar.png'})
  await browser.close()

asyncio.get_event_loop().run_until_complete(main())
