<html>
  <body>
    <div>
      <div>
	<span><b>Name</b></span>
	<span>{$values.display_name}</span>
      </div>
      <div>
	<span><b>Grant Application Received Date</b></span>
	<span>{$values.application_received_date}</span>
      </div>
      <div>
	<span><b>Grant Decision Date</b></span>
	<span>{$values.decision_date}</span>
      </div>
      <div>
	<span><b>Grant Money Transferred Date</b></span>
	<span>{$values.money_divansfer_date}</span>
      </div>
      <div>
	<span><b>Grant Due Date</b></span>
	<span>{$values.grant_due_date}</span>
      </div>
      <div>
	<span><b>Total Amount</b></span>
	<span>{$values.amount_total|crmMoney}</span>
      </div>
      <div>
	<span><b>Amount Requested</b></span>
	<span>{$values.amount_requested|crmMoney}</span>
      </div>
      <div>
	<span><b>Amount Granted</b></span>
	<span>{$values.amount_granted|crmMoney}</span>
      </div>
      {if $values.rationale}
      <div>
	<span><b>Rationale</b></span>
	<span>{$values.rationale}</span>
      </div>
      {/if}
      {if $values.noteId}
      <div>
	<span><b>Notes</b></span>
	<span>{$values.noteId}</span>
      </div>
      {/if}
      {if $values.custom}
      <div>
	{foreach from=$values.custom item=field key=id}
	<span><b>{$field.label}</b></span>
	<span>{$field.value}</span>
	{/foreach}
      </div>
      {/if}
      {if $values.attach}
      <div>
	{foreach from=$values.attach item=field key=id}
	<span><b>{$field.label}</b></span>
	<br/>
	<span>{$field.value}</span>
	<br/>
	{/foreach}
      </div>
      {/if}
    </div>
  </body>
</html>
	
