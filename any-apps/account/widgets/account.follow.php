<?php
if(!defined('ABSPATH'))exit('Access denied!');

class account_follow_widget extends Widget{

	public $table = 'user_follow';

	# 获取用户关注数
	public function get_user_follows($uid){
		return $this->db->one($this->table,"COUNT(`follow_id`)","user_id='$uid'");
	}
	# 获取用户粉丝数
	public function get_user_follower($uid){
		return $this->db->one($this->table,"COUNT(`user_id`)","follow_id='$uid'");
	}
	# 获取关注状态
	# @fid follow_id 被关注人的ID
	# @uid user_id  关注人的ID
	# @return integer 用户关注状态，格式为array('following'=>1,'follower'=>1)
	public function get_user_follow_state($fid,$uid){
		$follow_data = $this->db->rows($this->table, "*" ,"( user_id = '{$uid}' AND follow_id IN({$fid}) ) OR ( user_id IN({$fid}) AND follow_id = '{$uid}')");
		$follow_states = $this->_formatFollowState($uid,$fid,$follow_data);
		return $follow_states[$uid][$fid];
	}
	# 根据关注状态显示文本
	# @follow_states array('following'=>1,'follower'=>1)
	public function get_follow_text($follow_states){
		if($follow_states['following']==1&&$follow_states['follower']==1){
			return '互相关注';
		}else if($follow_states['following']==0){
			return '关注';
		}else if($follow_states['following']==1){
			return '已关注';
		}
	}
	# 格式化用户的关注数据
	private function _formatFollowState($uid, $fid, $follow_data) {
		$follow_states[$uid][$fid] = array (
			'following' => 0,
			'follower' => 0 
		);
		foreach ( $follow_data as $row ) {
			if ($row['user_id'] == $uid) {
				$follow_states[$row['user_id']][$row['follow_id']]['following'] = 1;
			} else if ($row['follow_id'] == $uid) {
				$follow_states[$row['follow_id']][$row['user_id']]['follower'] = 1;
			}
		}
		return $follow_states;
	}
}