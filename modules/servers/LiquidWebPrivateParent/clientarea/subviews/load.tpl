{if $stats}
    <table class="table table-framed table table-condensed" style="width: 100%">
        <tr>
            <td style="width: 25%"><b>Physical Memory:</b></td>
            <td style="width: 25%">{$stats.memory.physical.used} / {$stats.memory.physical.total}MB</td>
            <td colspan="2">
                <div class="progress {if $stats.memory.physical.percent > 60 && $stats.memory.physical.percent <= 90}bar-warning{elseif $stats.memory.physical.percent > 90}bar-danger{/if}" style="margin-bottom: 0;"> 
                    <div class="bar" style="width: {$stats.memory.physical.percent}%;"></div>
                </div>
            </td>
        </tr>
        <tr>
            <td><b>Virtual Memory:</b></td>
            <td>{$stats.memory.virtual.used} / {$stats.memory.virtual.total}MB</td>
            <td colspan="2">
                <div class="progress {if $stats.memory.virtual.percent > 60 && $stats.memory.virtual.percent <= 90}bar-warning{elseif $stats.memory.virtual.percent > 90}bar-danger{/if}" style="margin-bottom: 0;"> 
                    <div class="bar" style="width: {$stats.memory.virtual.percent}%;"></div>
                </div>
            </td>
        </tr>
        <tr>
            <td><b>Disk Usage:</b></td>
            <td>{$stats.disk.used} / {$stats.disk.total}GB</td>
            <td colspan="2">
                <div class="progress {if $stats.disk.percent > 60 &&  $stats.disk.percent < 90}bar-warning{elseif $stats.disk.percent >= 90}bar-danger{/if}" style="margin-bottom: 0;"> 
                    <div class="bar" style="width: {$stats.disk.percent}%;"></div>
                </div>
            </td>
        </tr>
        <tr>
            <td><b>Running Process:</b></td>
            <td>{$stats.proc.running}</td>
            <td><b>Total Process:</b></td>
            <td>{$stats.proc.total}</td>
        </tr>
        <tr>
            <td><b>Load Avg:</b></td>
            <td>1 min: {$stats.loadavg.one}</td>
            <td>5 min: {$stats.loadavg.five}</td>
            <td>15 min: {$stats.loadavg.fifteen}</td>
        </tr>
    </table>
{else}
    <p style="text-align: center"><b>Cannot display server load</b></p>
{/if}