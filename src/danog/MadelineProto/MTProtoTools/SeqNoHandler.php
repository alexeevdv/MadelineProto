<?php

/*
Copyright 2016-2018 Daniil Gentili
(https://daniil.it)
This file is part of MadelineProto.
MadelineProto is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
MadelineProto is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU Affero General Public License for more details.
You should have received a copy of the GNU General Public License along with MadelineProto.
If not, see <http://www.gnu.org/licenses/>.
*/

namespace danog\MadelineProto\MTProtoTools;

/**
 * Manages sequence number.
 */
trait SeqNoHandler
{
    public function generate_out_seq_no($datacenter, $content_related)
    {
        $in = $content_related ? 1 : 0;
        $value = $this->datacenter->sockets[$datacenter]->session_out_seq_no;
        $this->datacenter->sockets[$datacenter]->session_out_seq_no += $in;
        //$this->logger->logger("OUT $datacenter: $value + $in = ".$this->datacenter->sockets[$datacenter]->session_out_seq_no);
        return $value * 2 + $in;
    }

    public function check_in_seq_no($datacenter, $current_msg_id)
    {
        if (isset($this->datacenter->sockets[$datacenter]->incoming_messages[$current_msg_id]['seq_no']) && ($seq_no = $this->generate_in_seq_no($datacenter, $this->content_related($this->datacenter->sockets[$datacenter]->incoming_messages[$current_msg_id]['content']))) !== $this->datacenter->sockets[$datacenter]->incoming_messages[$current_msg_id]['seq_no']) {
            //$this->logger->logger('SECURITY WARNING: Seqno mismatch (should be '.$seq_no.', is '.$this->datacenter->sockets[$datacenter]->incoming_messages[$current_msg_id]['seq_no'].', '.$this->datacenter->sockets[$datacenter]->incoming_messages[$current_msg_id]['content']['_'].')', \danog\MadelineProto\Logger::ERROR);
        }
    }

    public function generate_in_seq_no($datacenter, $content_related)
    {
        $in = $content_related ? 1 : 0;
        $value = $this->datacenter->sockets[$datacenter]->session_in_seq_no;
        $this->datacenter->sockets[$datacenter]->session_in_seq_no += $in;
        //$this->logger->logger("IN $datacenter: $value + $in = ".$this->datacenter->sockets[$datacenter]->session_in_seq_no);
        return $value * 2 + $in;
    }

    public function content_related($method)
    {
        return isset($method['_']) ? !in_array($method['_'], self::NOT_CONTENT_RELATED) : true;
    }
}
