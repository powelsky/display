' Mandiri FX launcher
' Default : php -S 127.0.0.1:8080 (hidden, hanya jika belum jalan) lalu buka browser.
' lan     : bind 0.0.0.0:8080 supaya bisa dibuka dari HP di jaringan yang sama.
' noopen  : jalankan/verifikasi server saja, tanpa membuka browser.
' Contoh  : wscript start.vbs lan
'           wscript start.vbs lan noopen

Option Explicit

Const PORT = 8080

Dim fso, sh, baseDir, baseUrl, noOpen, lanMode, bindHost, probeOk, i

Set fso = CreateObject("Scripting.FileSystemObject")
Set sh = CreateObject("WScript.Shell")

baseDir = fso.GetParentFolderName(WScript.ScriptFullName)
baseUrl = "http://127.0.0.1:" & PORT & "/"

noOpen = False
lanMode = False

For i = 0 To WScript.Arguments.Count - 1
    Select Case LCase(WScript.Arguments(i))
        Case "noopen"
            noOpen = True
        Case "lan"
            lanMode = True
    End Select
Next

bindHost = "127.0.0.1"
If lanMode Then bindHost = "0.0.0.0"

probeOk = Probe(baseUrl)

If Not probeOk Then
    sh.CurrentDirectory = baseDir
    sh.Run "php -S " & bindHost & ":" & PORT & " -t """ & baseDir & """", 0, False

    For i = 1 To 24
        WScript.Sleep 250
        If Probe(baseUrl) Then
            probeOk = True
            Exit For
        End If
    Next
End If

If probeOk Then
    If lanMode And Not noOpen Then LanNotice
    If Not noOpen Then sh.Run baseUrl, 1, False
    WScript.Quit 0
End If

MsgBox "Server tidak bisa dijalankan di " & baseUrl & vbCrLf & _
       "Pastikan php.exe ada di PATH.", vbExclamation, "Mandiri FX"
WScript.Quit 1

Sub LanNotice()
    Dim lanIp, note
    lanIp = FirstLanIpv4()
    note = "Server LAN aktif di port " & PORT & "."
    If Len(lanIp) > 0 Then
        note = note & vbCrLf & vbCrLf & "Buka dari HP (Wi-Fi sama):" & vbCrLf & _
               "http://" & lanIp & ":" & PORT & "/"
    End If
    note = note & vbCrLf & vbCrLf & _
           "Install PWA (mode standalone) hanya berjalan dari origin HTTPS." & vbCrLf & _
           "Lewat http:// LAN, Chrome hanya menawarkan shortcut biasa."
    MsgBox note, vbInformation, "Mandiri FX (LAN)"
End Sub

' Returns True when the URL answers at all (any HTTP status counts).
Function Probe(url)
    Dim http
    On Error Resume Next
    Set http = CreateObject("MSXML2.ServerXMLHTTP.6.0")
    If Err.Number <> 0 Then
        Err.Clear
        Probe = False
        Exit Function
    End If
    http.setTimeouts 800, 800, 800, 1200
    http.open "GET", url, False
    http.send
    Probe = (Err.Number = 0)
    Err.Clear
    On Error GoTo 0
End Function

' Prefers 192.168.x.x, then 10.x.x.x, then any other non-loopback IPv4.
Function FirstLanIpv4()
    Dim wmi, list, nic, addr, preferred, fallback
    preferred = ""
    fallback = ""
    On Error Resume Next
    Set wmi = GetObject("winmgmts:\\.\root\cimv2")
    Set list = wmi.ExecQuery("SELECT IPAddress FROM Win32_NetworkAdapterConfiguration WHERE IPEnabled = True")
    For Each nic In list
        For Each addr In nic.IPAddress
            If InStr(addr, ":") = 0 And Left(addr, 4) <> "127." Then
                If Left(addr, 8) = "192.168." And preferred = "" Then
                    preferred = addr
                ElseIf Left(addr, 3) = "10." And fallback = "" Then
                    fallback = addr
                ElseIf fallback = "" Then
                    fallback = addr
                End If
            End If
        Next
    Next
    On Error GoTo 0
    If preferred <> "" Then
        FirstLanIpv4 = preferred
    Else
        FirstLanIpv4 = fallback
    End If
End Function
