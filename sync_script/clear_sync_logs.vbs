Set WinScriptHost = CreateObject("WScript.Shell")
WinScriptHost.Run Chr(34) & "C:\xampp\htdocs\web\sync_script\clear_sync_logs.bat" & Chr(34), 0
Set WinScriptHost = Nothing
