  <tr>
    <td colspan="3"><p class="waitset">【为了便于代码封装和维护，此处医生信息直接复用client_infor和avatar2个字段】</p></td>
  </tr>
 <tr>
    <td>client_infor</td>
    <td>作者信息</td>
    <td align="left" style="padding-left:20"><p>&nbsp;</p>
      <p>如果作者是妈咪，内容形如"client_id,1,昵称,年龄,孕育状态,所在地区,登录手机号"；</p>
      <p>如果作者是医生，内容形如"client_id,2,姓名,科室,职称级别,评价得分,所在医院,是否在线,登录手机号"；</p>
      <p>其中client_id用来转入作者主页，第2个数是用户类型 1：妈咪 2：医生</p>
      <p>&nbsp;</p></td>
  </tr>
  <tr>
    <td>avatar（如果有）</td>
    <td>作者头像</td>
    <td align="left" style="padding-left:20"><p>&nbsp;</p>
      <p>绝对地址</p>     
      <p>&nbsp;</p></td>
  </tr>
