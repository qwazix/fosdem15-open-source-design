#!/bin/bash
adb forward tcp:4747 tcp:4747
chromium-browser --disable-web-security index.html
