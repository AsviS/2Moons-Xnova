{block name="title" prepend}{$LNG.lm_messages}{/block}
{block name="content"}

<div class="row">
<div class="span9">

<h3 class="spec">{$LNG.lm_messages}</h3>
<div class="tab-content">
    <div class="tab-pane fade in active" id="tab1">

<table class="redesign" style="table-layout: fixed;">

<tr style="height:20px;">
</tr>
</table>

<table class="redesign" style="table-layout: fixed;">
	<tr>
		<th colspan="6">{$LNG.mg_overview}<span id="loading" style="display:none;"> ({$LNG.loading})</span></th>
	</tr>
		{foreach $CategoryList as $CategoryID => $CategoryRow}
		{if ($CategoryRow@iteration % 6) === 1}<tr>{/if}
		{if $CategoryRow@last && ($CategoryRow@iteration % 6) !== 0}<td>&nbsp;</td>{/if}
		<td style="word-wrap: break-word;color:{$CategoryRow.color};"><a href="#" onclick="Message.getMessages({$CategoryID});return false;" style="color:{$CategoryRow.color};">{$LNG.mg_type.{$CategoryID}}</a>
		<br><span id="unread_{$CategoryID}">{$CategoryRow.unread}</span>/<span id="total_{$CategoryID}">{$CategoryRow.total}</span>
		</td>
		{if $CategoryRow@last || ($CategoryRow@iteration % 6) === 0}</tr>{/if}
		{/foreach}</tr>
</table>

</div>


{/block}
{block name="script" append}
{if !empty($category)}
<script>$(function() {
	Message.getMessages({$category}, {$side});
})</script>
{/if}
{/block}