Set WinScriptHost = CreateObject("WScript.Shell")
WinScriptHost.Run Chr(34) & "C:\xampp\htdocs\bex\sync_script\sync_bp.bat" & Chr(34), 0
Set WinScriptHost = Nothing