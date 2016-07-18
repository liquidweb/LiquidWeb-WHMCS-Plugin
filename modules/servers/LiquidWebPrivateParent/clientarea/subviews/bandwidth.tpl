{if $stats}
    <table class="table table-framed table table-condensed" style="width: 100%">
        <tr>
            <td style="width: 25%"><b>Daily Avg:</b></td>
            <td style="width: 25%">{$stats.averages.day.both.GB}GB</td>
            <td style="width: 25%"><b>Weekly Avg:</b></td>
            <td style="width: 25%">{$stats.averages.week.both.GB}GB</td>
        </tr>
        <tr>
            <td><b>Monthly Avg:</b></td>
            <td>{$stats.averages.month.both.GB}GB</td>
            <td><b>Yearly Avg:</b></td>
            <td>{$stats.averages.year.both.GB}GB</td>
        </tr>
        <tr>
            <td><b>Included:</b></td>
            <td>{$stats.pricing.quota}GB</td>
            <td><b>Projected:</b></td>
            <td>{$stats.projected.out.GB}GB</td>
        </tr>
        <tr>
            <td><b>Current Usage:</b></td>
            <td>{$stats.actual.out.GB}/{$stats.pricing.quota}GB</td>
            <td colspan="2">
                <div class="progress {if ($stats.actual.out.GB/$stats.pricing.quota)*100 > 60 && ($stats.actual.out.GB/$stats.pricing.quota)*100 <= 90}bar-warning{elseif ($stats.actual.out.GB/$stats.pricing.quota)*100 > 90}bar-danger{/if}" style="margin-bottom: 0;"> 
                    <div class="bar" style="width: {math equation = "b/a*100" a=$stats.pricing.quota b=$stats.actual.out.GB format="%.0f"}%;"></div>
                </div>
            </td>
        </tr>
    </table>
{else}
    <p style="text-align: center"><b>Cannot display bandwidth statistics</b></p>
{/if}