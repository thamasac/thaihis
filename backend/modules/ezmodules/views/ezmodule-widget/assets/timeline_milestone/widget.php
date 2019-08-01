<?php

//$this->registerJsFile('@web/js-vis/vis-timeline-graph2d.min.js');

/* Params widget สามารถใช้งาน ตัวแปรต่อไปนี้ได้
'options' => $options,
'widget_config' => $widget_config,
'model' => $model, 
'modelOrigin'=>$modelOrigin,
'menu' => $menu,
'module' => $module,
'addon' => $addon,
'filter' => $filter,
'reloadDiv' => $reloadDiv,
'dataFilter' => $dataFilter,
'modelFilter' => $modelFilter,
'target' => $target,
    */
echo \backend\modules\gantt\classes\TimelineMSBuider::ui()->reloadDiv('display_milstone_content')->buildTimelineMS();

?>
<!--<p style='margin:0in;margin-bottom:.0001pt;text-align:center;font-size:16px;font-family:"Tahoma","sans-serif";'><span style="color:red;">Will be available soon!</span></p>

<p style='margin:0in;margin-bottom:.0001pt;text-align:center;font-size:16px;font-family:"Tahoma","sans-serif";'>
	<br>
</p>

<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;background:white;'><strong><span style="font-size:18px;color:#444444;">Part A: Outputs of the Project</span></strong></p>

<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;background:white;'>
	<br>
</p>

<table border="1" cellpadding="0" cellspacing="0" style="width:100.0%;background:white;" width="100%">
	<tbody>
		<tr>
			<td style="width: 39.72%;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="39.39393939393939%">

				<p style='margin:0cm;margin-bottom:7.5pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'><strong><span style="color:#444444;">Outputs</span></strong></p>
			</td>
			<td style="width: 30.14%;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="30.303030303030305%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'><strong><span style="color:#444444;">Link</span></strong></p>
			</td>
			<td style="width: 30.14%;padding: 0cm;vertical-align: top;" valign="top" width="30.303030303030305%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'><strong><span style="color:#444444;">Modules of Origin</span></strong></p>
			</td>
		</tr>
		<tr>
			<td style="width: 39.72%;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="39.39393939393939%">

				<p style='margin:0cm;margin-bottom:7.5pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'><span style='font-size:14px;font-family:"Helvetica",sans-serif;color:#444444;'>1. Trial Master File (TMF) Archives</span></p>
			</td>
			<td style="width: 30.14%;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="30.303030303030305%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">[</span><span style="color:#0066FF;">Download ZIP</span><span style="color:#444444;">]</span></p>
			</td>
			<td style="width: 30.14%;padding: 0cm;vertical-align: top;" valign="top" width="30.303030303030305%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">TMF</span></p>
			</td>
		</tr>
		<tr>
			<td style="width: 39.72%;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="39.39393939393939%">

				<p style='margin:0cm;margin-bottom:7.5pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'><span style='font-size:14px;font-family:"Helvetica",sans-serif;color:#444444;'>2. Gannt Charts of Project&rsquo;s activities</span></p>
			</td>
			<td style="width: 30.14%;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="30.303030303030305%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">[</span><a href="/gantt/gantt/gantt-hvtexport" rel="noopener noreferrer" target="_blank"><span style="color:#0066FF;">Download PDF</span></a><span style="color:#444444;">]</span></p>
			</td>
			<td style="width: 30.14%;padding: 0cm;vertical-align: top;" valign="top" width="30.303030303030305%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">PMS: Core Essential</span></p>
			</td>
		</tr>
		<tr>
			<td style="width: 39.72%;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="39.39393939393939%">

				<p style='margin:0cm;margin-bottom:7.5pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'><span style='font-size:14px;font-family:"Helvetica",sans-serif;color:#444444;'>3. Timeline and Milestone&nbsp;</span></p>
			</td>
			<td style="width: 30.14%;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="30.303030303030305%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">[</span><a href="/gantt/timeline-milestone/index" rel="noopener noreferrer" target="_blank"><span style="color:#0066FF;">Download PDF</span></a><span style="color:#444444;">]</span></p>
			</td>
			<td style="width: 30.14%;padding: 0cm;vertical-align: top;" valign="top" width="30.303030303030305%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">PMS: Timeline &amp; Milestone</span></p>
			</td>
		</tr>
		<tr>
			<td style="width: 39.72%;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="39.39393939393939%">

				<p style='margin:0cm;margin-bottom:7.5pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'><span style='font-size:14px;font-family:"Helvetica",sans-serif;color:#444444;'>4. Visit schedule</span></p>
			</td>
			<td style="width: 30.14%;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="30.303030303030305%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">[</span><span style="color:#0066FF;">Download PDF</span><span style="color:#444444;">]</span></p>
			</td>
			<td style="width: 30.14%;padding: 0cm;vertical-align: top;" valign="top" width="30.303030303030305%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">SMS</span></p>
			</td>
		</tr>
		<tr>
			<td style="width: 39.72%;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="39.39393939393939%">

				<p style='margin:0cm;margin-bottom:7.5pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'><span style='font-size:14px;font-family:"Helvetica",sans-serif;color:#444444;'>5. Monitoring Visit Reports</span></p>
			</td>
			<td style="width: 30.14%;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="30.303030303030305%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">[</span><span style="color:#0066FF;">Download ZIP</span><span style="color:#444444;">]</span></p>
			</td>
			<td style="width: 30.14%;padding: 0cm;vertical-align: top;" valign="top" width="30.303030303030305%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">QMS: Monitor</span></p>
			</td>
		</tr>
		<tr>
			<td style="width: 39.72%;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="39.39393939393939%">

				<p style='margin:0cm;margin-bottom:7.5pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'><span style='font-size:14px;font-family:"Helvetica",sans-serif;color:#444444;'>6. Auditor&rsquo;s Reports</span></p>
			</td>
			<td style="width: 30.14%;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="30.303030303030305%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">[</span><span style="color:#0066FF;">Download ZIP</span><span style="color:#444444;">]</span></p>
			</td>
			<td style="width: 30.14%;padding: 0cm;vertical-align: top;" valign="top" width="30.303030303030305%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">QMS: Auditor</span></p>
			</td>
		</tr>
		<tr>
			<td style="width: 39.72%;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="39.39393939393939%">

				<p style='margin:0cm;margin-bottom:7.5pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'><span style='font-size:14px;font-family:"Helvetica",sans-serif;color:#444444;'>7. CAPA Report</span></p>
			</td>
			<td style="width: 30.14%;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="30.303030303030305%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">[</span><span style="color:#0066FF;">Download ZIP</span><span style="color:#444444;">]</span></p>
			</td>
			<td style="width: 30.14%;padding: 0cm;vertical-align: top;" valign="top" width="30.303030303030305%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">QMS: CAPA</span></p>
			</td>
		</tr>
		<tr>
			<td style="width: 39.72%;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="39.39393939393939%">

				<p style='margin:0cm;margin-bottom:7.5pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'><span style='font-size:14px;font-family:"Helvetica",sans-serif;color:#444444;'>8. eCRFs: All Case Report Forms</span></p>
			</td>
			<td style="width: 30.14%;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="30.303030303030305%">
				<br>
			</td>
			<td style="width: 30.14%;padding: 0cm;vertical-align: top;" valign="top" width="30.303030303030305%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'>
					<br>
				</p>
			</td>
		</tr>
		<tr>
			<td style="width: 39.72%;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="39.39393939393939%">

				<p style='margin:0cm;margin-bottom:7.5pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'><span style='font-size:14px;font-family:"Helvetica",sans-serif;color:#444444;'>&nbsp; &nbsp;8.1) Paper CRFs</span></p>
			</td>
			<td style="width: 30.14%;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="30.303030303030305%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">[</span><span style="color:#0066FF;">Download PDF</span><span style="color:#444444;">]</span></p>
			</td>
			<td style="width: 30.14%;padding: 0cm;vertical-align: top;" valign="top" width="30.303030303030305%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">TMF: CRF</span></p>
			</td>
		</tr>
		<tr>
			<td style="width: 39.72%;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="39.39393939393939%">

				<p style='margin:0cm;margin-bottom:7.5pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'><span style='font-size:14px;font-family:"Helvetica",sans-serif;color:#444444;'>&nbsp; &nbsp;8.2) eCRFs&nbsp;</span></p>
			</td>
			<td style="width: 30.14%;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="30.303030303030305%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">[</span><span style="color:#0066FF;">Click CRF Archives</span><span style="color:#444444;">]</span></p>
			</td>
			<td style="width: 30.14%;padding: 0cm;vertical-align: top;" valign="top" width="30.303030303030305%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">eCRF</span></p>
			</td>
		</tr>
		<tr>
			<td style="width: 39.72%;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="39.39393939393939%">

				<p style='margin:0cm;margin-bottom:7.5pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'><span style='font-size:14px;font-family:"Helvetica",sans-serif;color:#444444;'>9. All research data by eCRFs, saved in CSV files</span></p>
			</td>
			<td style="width: 30.14%;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="30.303030303030305%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">[</span><span style="color:#0066FF;">Download ZIP</span><span style="color:#444444;">]</span></p>
			</td>
			<td style="width: 30.14%;padding: 0cm;vertical-align: top;" valign="top" width="30.303030303030305%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">eCRF</span></p>
			</td>
		</tr>
		<tr>
			<td style="width: 39.72%;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="39.39393939393939%">

				<p style='margin:0cm;margin-bottom:7.5pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'><span style='font-size:14px;font-family:"Helvetica",sans-serif;color:#444444;'>10. Data and Annotated CRFs in one executable application&nbsp;</span></p>
			</td>
			<td style="width: 30.14%;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="30.303030303030305%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">[</span><span style="color:#0066FF;">Download Purify Application</span><span style="color:#444444;">]</span></p>
			</td>
			<td style="width: 30.14%;padding: 0cm;vertical-align: top;" valign="top" width="30.303030303030305%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">eCRF</span></p>
			</td>
		</tr>
		<tr>
			<td style="width: 39.72%;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="39.39393939393939%">

				<p style='margin:0cm;margin-bottom:7.5pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'><span style='font-size:14px;font-family:"Helvetica",sans-serif;color:#444444;'>11. Financial Reports</span></p>
			</td>
			<td style="width: 30.14%;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="30.303030303030305%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">[</span><span style="color:#0066FF;">Download ZIP</span><span style="color:#444444;">]</span></p>
			</td>
			<td style="width: 30.14%;padding: 0cm;vertical-align: top;" valign="top" width="30.303030303030305%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">FMS</span></p>
			</td>
		</tr>
		<tr>
			<td style="width: 39.72%;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="39.39393939393939%">

				<p style='margin:0cm;margin-bottom:7.5pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'><span style='font-size:14px;font-family:"Helvetica",sans-serif;color:#444444;'>12. Project Members</span></p>
			</td>
			<td style="width: 30.14%;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="30.303030303030305%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">[</span><span style="color:#0066FF;">Download ZIP</span><span style="color:#444444;">]</span></p>
			</td>
			<td style="width: 30.14%;padding: 0cm;vertical-align: top;" valign="top" width="30.303030303030305%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:left;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">MMS</span></p>
			</td>
		</tr>
	</tbody>
</table>

<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;background:white;'>
	<br>
</p>

<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;background:white;'><strong><span style="font-size:18px;color:#444444;">Part B: Full Report Retrieved from EzWriting</span></strong></p>

<table border="1" cellpadding="0" cellspacing="0" style="margin-left: -0.25pt;width: 100%;">
	<tbody>
		<tr>
			<td style="width: 241pt;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="49.57507082152974%">

				<p><span style="color:#444444;">1. Protocol Synopsis</span></p>
			</td>
			<td style="width: 226.75pt;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="50.42492917847026%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">[</span><span style="color:#2969B0;">Download MS-Word</span><span style="color:#444444;">]&nbsp;[</span><span style="color:#2969B0;">Download PDF</span><span style="color:#444444;">]</span></p>
			</td>
		</tr>
		<tr>
			<td style="width: 241pt;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="49.57507082152974%">

				<p><span style="color:#444444;">2. Full Protocol</span></p>
			</td>
			<td style="width: 226.75pt;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="50.42492917847026%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">[</span><span style="color:#2969B0;">Download MS-Word</span><span style="color:#444444;">]&nbsp;[</span><span style="color:#2969B0;">Download PDF</span><span style="color:#444444;">]</span></p>
			</td>
		</tr>
		<tr>
			<td style="width: 241pt;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="49.57507082152974%">

				<p><span style="color:#444444;">3. Statistical Analysis Plan</span></p>
			</td>
			<td style="width: 226.75pt;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="50.42492917847026%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">[</span><span style="color:#2969B0;">Download MS-Word</span><span style="color:#444444;">]&nbsp;[</span><span style="color:#2969B0;">Download PDF</span><span style="color:#444444;">]</span></p>
			</td>
		</tr>
		<tr>
			<td style="width: 241pt;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="49.57507082152974%">

				<p><span style="color:#444444;">4. Data Files (e.g., Stata data file)</span></p>
			</td>
			<td style="width: 226.75pt;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="50.42492917847026%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">[</span><span style="color:#2969B0;">Download Data File</span><span style="color:#444444;">]&nbsp;</span></p>
			</td>
		</tr>
		<tr>
			<td style="width: 241pt;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="49.57507082152974%">

				<p><span style="color:#444444;">5. Statistical analysis command files (e.g., Stata do file )</span></p>
			</td>
			<td style="width: 226.75pt;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="50.42492917847026%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">[</span><span style="color:#2969B0;">Download File</span><span style="color:#444444;">]</span></p>
			</td>
		</tr>
		<tr>
			<td style="width: 241pt;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="49.57507082152974%">

				<p><span style="color:#444444;">6. Full Study Report</span></p>
			</td>
			<td style="width: 226.75pt;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="50.42492917847026%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">[</span><span style="color:#2969B0;">Download MS-Word</span><span style="color:#444444;">]&nbsp;[</span><span style="color:#2969B0;">Download PDF</span><span style="color:#444444;">]</span></p>
			</td>
		</tr>
	</tbody>
</table>

<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;background:white;'>
	<br>
</p>

<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;background:white;'><strong><span style="font-size:18px;color:#444444;">Part C: Publication Processes Retrieved from EzPublications</span></strong></p>

<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;background:white;'>
	<br>
</p>

<ol start="1" style="margin-bottom:0cm;margin-top:0cm;" type="1">
	<li style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;color:#444444;background:white;'>Paper 1: Title __________________________________</li>
</ol>

<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;margin-left:36.0pt;background:white;'>
	<br>
</p>

<table border="1" cellpadding="0" cellspacing="0" style="width: 100%;margin-left: 35.2pt;" width="0">
	<tbody>
		<tr>
			<td style="width: 233.9pt;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="54.07279029462738%">

				<p><span style="color:#444444;">1. Mock Abstract&nbsp;</span></p>
			</td>
			<td style="width: 7cm;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="45.92720970537262%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">[</span><span style="color:#2969B0;">Download Mock Abstract</span><span style="color:#444444;">]&nbsp;</span></p>
			</td>
		</tr>
		<tr>
			<td style="width: 233.9pt;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="54.07279029462738%">

				<p><span style="color:#444444;">2. Completed Abstract</span></p>
			</td>
			<td style="width: 7cm;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="45.92720970537262%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">[</span><span style="color:#2969B0;">Download Completed Abstract</span><span style="color:#444444;">]</span></p>
			</td>
		</tr>
		<tr>
			<td style="width: 233.9pt;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="54.07279029462738%">

				<p><span style="color:#444444;">3. Mock Manuscript&nbsp;</span></p>
			</td>
			<td style="width: 7cm;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="45.92720970537262%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">[</span><span style="color:#2969B0;">Download Mock Manuscript</span><span style="color:#444444;">]&nbsp;</span></p>
			</td>
		</tr>
		<tr>
			<td style="width: 233.9pt;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="54.07279029462738%">

				<p><span style="color:#444444;">4. Completed Manuscript</span></p>
			</td>
			<td style="width: 7cm;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="45.92720970537262%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">[</span><span style="color:#2969B0;">Download Completed Manuscript</span><span style="color:#444444;">]&nbsp;</span></p>
			</td>
		</tr>
		<tr>
			<td style="width: 233.9pt;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="54.07279029462738%">

				<p><span style="color:#444444;">5. Power Pint Presentation</span></p>
			</td>
			<td style="width: 7cm;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="45.92720970537262%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">[</span><span style="color:#2969B0;">Download PPT</span><span style="color:#444444;">]</span></p>
			</td>
		</tr>
		<tr>
			<td style="width: 233.9pt;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="54.07279029462738%">

				<p><span style="color:#444444;">6. Journal Submission Process</span></p>
			</td>
			<td style="width: 7cm;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="45.92720970537262%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">[</span><span style="color:#2969B0;">Download PDF</span><span style="color:#444444;">]</span></p>
			</td>
		</tr>
		<tr>
			<td style="width: 233.9pt;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="54.07279029462738%">

				<p><span style="color:#444444;">7. Respond to the Reviewers</span></p>
			</td>
			<td style="width: 7cm;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="45.92720970537262%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">[</span><span style="color:#2969B0;">Download PDF</span><span style="color:#444444;">]</span></p>
			</td>
		</tr>
		<tr>
			<td style="width: 233.9pt;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="54.07279029462738%">

				<p><span style="color:#444444;">8. Published Articles</span></p>
			</td>
			<td style="width: 7cm;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="45.92720970537262%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;'>
					<br>
				</p>
			</td>
		</tr>
		<tr>
			<td style="width: 233.9pt;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="54.07279029462738%">

				<ul style="margin-bottom:0cm;">
					<li><span style="color:#444444;">Abstract</span></li>
				</ul>
			</td>
			<td style="width: 7cm;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="45.92720970537262%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">[</span><span style="color:#2969B0;">Download PDF</span><span style="color:#444444;">]</span></p>
			</td>
		</tr>
		<tr>
			<td style="width: 233.9pt;padding: 0cm 5.4pt;height: 14.15pt;vertical-align: top;" valign="top" width="54.07279029462738%">

				<ul style="margin-bottom:0cm;">
					<li><span style="color:#444444;">Full Text</span></li>
				</ul>
			</td>
			<td style="width: 7cm;padding: 0cm 5.4pt;height: 14.15pt;vertical-align: top;" valign="top" width="45.92720970537262%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">[</span><span style="color:#2969B0;">Download PDF</span><span style="color:#444444;">]</span></p>
			</td>
		</tr>
		<tr>
			<td style="width: 233.9pt;padding: 0cm 5.4pt;height: 14.15pt;vertical-align: top;" valign="top" width="54.07279029462738%">

				<ul style="margin-bottom:0cm;">
					<li><span style="color:#444444;">DOI of the article:&nbsp;</span></li>
				</ul>
			</td>
			<td style="width: 7cm;padding: 0cm 5.4pt;height: 14.15pt;vertical-align: top;" valign="top" width="45.92720970537262%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">___________</span></p>
			</td>
		</tr>
		<tr>
			<td style="width: 233.9pt;padding: 0cm 5.4pt;height: 14.15pt;vertical-align: top;" valign="top" width="54.07279029462738%">

				<ul style="margin-bottom:0cm;">
					<li><span style="color:#444444;">URL of the article:&nbsp;</span></li>
				</ul>
			</td>
			<td style="width: 7cm;padding: 0cm 5.4pt;height: 14.15pt;vertical-align: top;" valign="top" width="45.92720970537262%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">___________</span></p>
			</td>
		</tr>
		<tr>
			<td style="width: 233.9pt;padding: 0cm 5.4pt;height: 14.15pt;vertical-align: top;" valign="top" width="54.07279029462738%">

				<ul style="margin-bottom:0cm;">
					<li><span style="color:#444444;">Suggested Citation:</span></li>
				</ul>
			</td>
			<td style="width: 7cm;padding: 0cm 5.4pt;height: 14.15pt;vertical-align: top;" valign="top" width="45.92720970537262%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">___________</span></p>
			</td>
		</tr>
	</tbody>
</table>

<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;'>
	<br>
</p>

<ol start="2" style="margin-bottom:0cm;margin-top:0cm;" type="1">
	<li style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;color:#444444;background:white;'>Paper 2: Title __________________________________</li>
</ol>

<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;margin-left:36.0pt;background:white;'>
	<br>
</p>

<table border="1" cellpadding="0" cellspacing="0" style="width: 100%;margin-left: 35.2pt;" width="0">
	<tbody>
		<tr>
			<td style="width: 233.9pt;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="54.07279029462738%">

				<p><span style="color:#444444;">1. Mock Abstract&nbsp;</span></p>
			</td>
			<td style="width: 7cm;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="45.92720970537262%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">[</span><span style="color:#2969B0;">Download Mock Abstract</span><span style="color:#444444;">]&nbsp;</span></p>
			</td>
		</tr>
		<tr>
			<td style="width: 233.9pt;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="54.07279029462738%">

				<p><span style="color:#444444;">2. Completed Abstract</span></p>
			</td>
			<td style="width: 7cm;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="45.92720970537262%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">[</span><span style="color:#2969B0;">Download Completed Abstract</span><span style="color:#444444;">]</span></p>
			</td>
		</tr>
		<tr>
			<td style="width: 233.9pt;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="54.07279029462738%">

				<p><span style="color:#444444;">3. Mock Manuscript&nbsp;</span></p>
			</td>
			<td style="width: 7cm;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="45.92720970537262%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">[</span><span style="color:#2969B0;">Download Mock Manuscript</span><span style="color:#444444;">]&nbsp;</span></p>
			</td>
		</tr>
		<tr>
			<td style="width: 233.9pt;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="54.07279029462738%">

				<p><span style="color:#444444;">4. Completed Manuscript</span></p>
			</td>
			<td style="width: 7cm;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="45.92720970537262%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">[</span><span style="color:#2969B0;">Download Completed Manuscript</span><span style="color:#444444;">]&nbsp;</span></p>
			</td>
		</tr>
		<tr>
			<td style="width: 233.9pt;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="54.07279029462738%">

				<p><span style="color:#444444;">5. Power Pint Presentation</span></p>
			</td>
			<td style="width: 7cm;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="45.92720970537262%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">[</span><span style="color:#2969B0;">Download PPT</span><span style="color:#444444;">]</span></p>
			</td>
		</tr>
		<tr>
			<td style="width: 233.9pt;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="54.07279029462738%">

				<p><span style="color:#444444;">6. Journal Submission Process</span></p>
			</td>
			<td style="width: 7cm;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="45.92720970537262%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">[</span><span style="color:#2969B0;">Download PDF</span><span style="color:#444444;">]</span></p>
			</td>
		</tr>
		<tr>
			<td style="width: 233.9pt;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="54.07279029462738%">

				<p><span style="color:#444444;">7. Respond to the Reviewers</span></p>
			</td>
			<td style="width: 7cm;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="45.92720970537262%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">[</span><span style="color:#2969B0;">Download PDF</span><span style="color:#444444;">]</span></p>
			</td>
		</tr>
		<tr>
			<td style="width: 233.9pt;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="54.07279029462738%">

				<p><span style="color:#444444;">8. Published Articles</span></p>
			</td>
			<td style="width: 7cm;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="45.92720970537262%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;'>
					<br>
				</p>
			</td>
		</tr>
		<tr>
			<td style="width: 233.9pt;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="54.07279029462738%">

				<ul style="margin-bottom:0cm;">
					<li><span style="color:#444444;">Abstract</span></li>
				</ul>
			</td>
			<td style="width: 7cm;padding: 0cm 5.4pt;vertical-align: top;" valign="top" width="45.92720970537262%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">[</span><span style="color:#2969B0;">Download PDF</span><span style="color:#444444;">]</span></p>
			</td>
		</tr>
		<tr>
			<td style="width: 233.9pt;padding: 0cm 5.4pt;height: 14.15pt;vertical-align: top;" valign="top" width="54.07279029462738%">

				<ul style="margin-bottom:0cm;">
					<li><span style="color:#444444;">Full Text</span></li>
				</ul>
			</td>
			<td style="width: 7cm;padding: 0cm 5.4pt;height: 14.15pt;vertical-align: top;" valign="top" width="45.92720970537262%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">[</span><span style="color:#2969B0;">Download PDF</span><span style="color:#444444;">]</span></p>
			</td>
		</tr>
		<tr>
			<td style="width: 233.9pt;padding: 0cm 5.4pt;height: 14.15pt;vertical-align: top;" valign="top" width="54.07279029462738%">

				<ul style="margin-bottom:0cm;">
					<li><span style="color:#444444;">DOI of the article:&nbsp;</span></li>
				</ul>
			</td>
			<td style="width: 7cm;padding: 0cm 5.4pt;height: 14.15pt;vertical-align: top;" valign="top" width="45.92720970537262%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">___________</span></p>
			</td>
		</tr>
		<tr>
			<td style="width: 233.9pt;padding: 0cm 5.4pt;height: 14.15pt;vertical-align: top;" valign="top" width="54.07279029462738%">

				<ul style="margin-bottom:0cm;">
					<li><span style="color:#444444;">URL of the article:&nbsp;</span></li>
				</ul>
			</td>
			<td style="width: 7cm;padding: 0cm 5.4pt;height: 14.15pt;vertical-align: top;" valign="top" width="45.92720970537262%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">___________</span></p>
			</td>
		</tr>
		<tr>
			<td style="width: 233.9pt;padding: 0cm 5.4pt;height: 14.15pt;vertical-align: top;" valign="top" width="54.07279029462738%">

				<ul style="margin-bottom:0cm;">
					<li><span style="color:#444444;">Suggested Citation:</span></li>
				</ul>
			</td>
			<td style="width: 7cm;padding: 0cm 5.4pt;height: 14.15pt;vertical-align: top;" valign="top" width="45.92720970537262%">

				<p style='margin:0cm;margin-bottom:.0001pt;text-align:justify;font-size:16px;font-family:"Tahoma",sans-serif;'><span style="color:#444444;">___________</span></p>
			</td>
		</tr>
	</tbody>
</table>-->
