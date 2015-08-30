<?xml version="1.0"?>
<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:param name="venue_title" />
  <xsl:param name="venue_link" />
  <xsl:param name="submit_datetime" />
  <xsl:param name="city_name" />
  <xsl:param name="event_date" />
  
  <xsl:template match="/">
    <div class="user-input">
      <div class="user-input-inner clearfix">
        <span class="submitted"> 
          <em><xsl:value-of select="$lbl_postdate"/></em>: <xsl:value-of select="$submit_datetime" />
        </span><br />
        <xsl:if test="$uid != ''">
          <span class="submitted">
            <xsl:value-of select="$lbl_user"/>:

            <a href="/user/">
              <xsl:attribute name="href">
                <xsl:value-of select="$user_link" />
              </xsl:attribute>
              <xsl:value-of select="$user" />
            </a>
          </span>
 

        </xsl:if>
 
        <xsl:apply-templates/>
      </div>
    </div>
  </xsl:template>
  <xsl:template match="input[soort[text()='concert']]">
       <xsl:call-template name="concert" />
  </xsl:template>
  <xsl:template match="input[soort[text()='festival']]">
       <xsl:call-template name="concert" />
  </xsl:template>
  <xsl:template match="input[soort[text()='iets_anders']]">
       <xsl:call-template name="concert" />
  </xsl:template>

  <xsl:template name="concert">
    <h3>
      <a class="title-link" target="_blank">
        <xsl:attribute name="href">
            <xsl:value-of select="./link" />
        </xsl:attribute>
        <xsl:value-of select="./title"/>
      </a>
    </h3>
    <div class="muziek_cell1">
      <ul>
      <xsl:if test="$venue_title != ''">
        <li>
        <a class="venue-link" target="_blank">
          <xsl:attribute name="href">
            <xsl:value-of select="$venue_link"/>
          </xsl:attribute>
          <strong>
          <xsl:value-of select="$venue_title"/>
          </strong>
        </a>
        </li> 
      </xsl:if>
        <li class="date"><span class="labels"><xsl:value-of select="$lbl_date"/>: </span>
          <em><xsl:value-of select="$event_date"/>
          </em>
        </li>
        <li class="stad">
          <span class="labels"><xsl:value-of select="$lbl_place"/>: </span>
          <em>
          <xsl:choose>
            <xsl:when test="$city_name != ''">
               <xsl:value-of select="$city_name"/>
            </xsl:when>
            <xsl:otherwise>
               <xsl:value-of select="./city"/>
            </xsl:otherwise>
          </xsl:choose>
          </em>
        </li> 

        <li class="soort">
	        <span class="labels"><xsl:value-of select="$lbl_soort"/>: </span>
           <em><xsl:value-of select="$soort" /></em></li>
        <li>
          <a class="title-link" target="_blank">
            <xsl:attribute name="href">
                <xsl:value-of select="./link" />
            </xsl:attribute>
            <xsl:value-of select="./link"/>
          </a>
    
        </li>
      </ul>
    </div>
    <div class="muziek_cell2">
      <xsl:if test="$venue_title = ''"> 
        <pre>
           <xsl:value-of select="./venue_freetext"/>
        </pre>
      </xsl:if>
       
    </div>
    <div class="muziek_cell3">
       <pre>
            <xsl:value-of select="./description"/>
        </pre>
    </div>
  </xsl:template>
</xsl:stylesheet>
