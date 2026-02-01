$path = 'public/react/react/index-ClCmhI9E.js'
if (Test-Path $path) {
    $content = Get-Content $path -Raw
    $match = [regex]::Match($content, 'Plain.{1,50}Clean')
    if ($match.Success) {
        $index = $match.Index
        $start = [Math]::Max(0, $index - 50)
        $length = [Math]::Min(300, $content.Length - $start)
        Write-Output "Found sequence at $index :"
        Write-Output $content.Substring($start, $length)
    } else {
        Write-Output "Sequence 'Plain...Clean' not found."
    }
}
