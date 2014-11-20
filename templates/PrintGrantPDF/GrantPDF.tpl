<html>
  <body>
    <table>
      <tr>
	<td><b>Name</b></td>
	<td>{$values.display_name}</td>
      </tr>
      <tr>
	<td><b>Grant Application Received Date</b></td>
	<td>{$values.application_received_date}</td>
      </tr>
      <tr>
	<td><b>Grant Decision Date</b></td>
	<td>{$values.decision_date}</td>
      </tr>
      <tr>
	<td><b>Grant Money Transferred Date</b></td>
	<td>{$values.money_transfer_date}</td>
      </tr>
      <tr>
	<td><b>Grant Due Date</b></td>
	<td>{$values.grant_due_date}</td>
      </tr>
      <tr>
	<td><b>Total Amount</b></td>
	<td>{$values.amount_total|crmMoney}</td>
      </tr>
      <tr>
	<td><b>Amount Requested</b></td>
	<td>{$values.amount_requested|crmMoney}</td>
      </tr>
      <tr>
	<td><b>Amount Granted</b></td>
	<td>{$values.amount_granted|crmMoney}</td>
      </tr>
      {if $values.rationale}
      <tr>
	<td><b>Rationale</b></td>
	<td>{$values.rationale}</td>
      </tr>
      {/if}
      {if $values.noteId}
      <tr>
	<td><b>Notes</b></td>
	<td>{$values.noteId}</td>
      </tr>
      {/if}
      {if $values.custom}
      <tr>
	{foreach from=$values.custom item=field key=id}
	<td><b>{$field.label}</b></td>
	<td>{$field.value}</td>
	{/foreach}
      </tr>
      {/if}
      {if $values.attach}
      <tr>
	{foreach from=$values.attach item=field key=id}
	<td><b>{$field.label}</b></td>
	<td>{$field.value}</td>
	{/foreach}
      </tr>
      {/if}
    </table>
  </body>
</html>
	
