{block name="environment_notice_notice_index"}
    {block name="environment_notice_notice_index_style"}
        <style>
            {block name="environment_notice_notice_index_style_slots"}
                {foreach $environment_notice.slots as $slot}
                    {block name="environment_notice_notice_index_style_slot"}
                        {$slot.css}
                    {/block}
                {/foreach}
            {/block}
        </style>
    {/block}

    {block name="environment_notice_notice_index_slots"}
        {foreach $environment_notice.slots as $slot}
            {block name="environment_notice_notice_index_slot"}
                <div id="slot-{$slot.id}">
                    {block name="environment_notice_notice_index_slot_notices"}
                        {foreach $slot.notices as $notice}
                            {block name="environment_notice_notice_index_slot_notice"}
                                {$notice.message|nl2br}
                            {/block}
                        {/foreach}
                    {/block}
                </div>
            {/block}
        {/foreach}
    {/block}
{/block}
