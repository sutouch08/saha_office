Set WinScriptHost = CreateObject("WScript.Shell")
WinScriptHost.Run Chr(34) & "C:\xampp\htdocs\web\sync_script\sync_sales_order.bat" & Chr(34), 0
Set WinScriptHost = Nothing
