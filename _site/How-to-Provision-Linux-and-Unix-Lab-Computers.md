<!DOCTYPE html>
<html lang="en-US">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Begin Jekyll SEO tag v2.5.0 -->
<title>How to Provision Linux and Unix Lab Computers | vcl</title>
<meta name="generator" content="Jekyll v3.7.4" />
<meta property="og:title" content="How to Provision Linux and Unix Lab Computers" />
<meta property="og:locale" content="en_US" />
<meta name="description" content="Documentation for the Apache VCL (Virtual Computing Lab) project" />
<meta property="og:description" content="Documentation for the Apache VCL (Virtual Computing Lab) project" />
<link rel="canonical" href="http://localhost:4000/How-to-Provision-Linux-and-Unix-Lab-Computers.md" />
<meta property="og:url" content="http://localhost:4000/How-to-Provision-Linux-and-Unix-Lab-Computers.md" />
<meta property="og:site_name" content="vcl" />
<script type="application/ld+json">
{"url":"http://localhost:4000/How-to-Provision-Linux-and-Unix-Lab-Computers.md","headline":"How to Provision Linux and Unix Lab Computers","description":"Documentation for the Apache VCL (Virtual Computing Lab) project","@type":"WebPage","@context":"http://schema.org"}</script>
<!-- End Jekyll SEO tag -->

    <link rel="stylesheet" href="/assets/css/style.css?v=5a462ccebd38c405a5d0383f2220b85b4c0a08c1">
  </head>
  <body>
    <div class="container-lg px-3 my-5 markdown-body">

      <h1><a href="http://localhost:4000/">How to Provision Linux and Unix Lab Computers</a></h1> 


      <p>The Lab.pm provisioning module is used to broker access to standalone pre-installed Linux or Solaris machines. These machines could be in an existing walk-in computer lab or racked in a server room.</p>

<p>There are four main parts needed to setup a standalone machine to use with the Lab.pm module.</p>

<ol>
  <li>a non-root account called vclstaff on the target machines</li>
  <li>ssh idenitity key for vclstaff account, this key is used by the vcld process on the management node</li>
  <li>ssh service running on port 24 of the target machines</li>
  <li>vclclientd running on the target machines, vclclientd in the bin directory of the vcld release</li>
</ol>

<p>For distribution to a large set of machines, an rpm or package could be created to distribute vclclientd and related files.</p>

<h3 id="how-it-works">How it Works</h3>

<p>The Lab.pm module confirms an assigned node or lab machine is accessible using the ssh identity key on port 24. If this succeeds, then a small configuration file with the state, user’s id and the users’ remote IP address is sent to the node along with a flag to trigger the vclclientd process to either open or close the remote access port. Currently this module only supports Linux and Solaris lab machines.</p>

<h3 id="how-to-setup">How to setup:</h3>

<p>All commands are run as root.</p>

<ol>
  <li>Create the non-root vclstaff account on target machine</li>
</ol>

<p>On Linux;</p>

<div class="highlighter-rouge"><div class="highlight"><pre class="highlight"><code>    useradd -d /home/vclstaff -m vclstaff
</code></pre></div></div>

<ol>
  <li>
    <p>Generate ssh identity keys for vclstaff account. Do not enter a passphrase for the key, just hit enter when prompted.</p>

    <div class="highlighter-rouge"><div class="highlight"><pre class="highlight"><code> su - vclstaff
 ssh-keygen -t rsa
 Generating public/private rsa key pair.
 Enter file in which to save the key (/home/vclstaff/.ssh/id_rsa):
 Created directory '/home/vclstaff/.ssh'.
 Enter passphrase (empty for no passphrase):
 Enter same passphrase again:
 Your identification has been saved in /home/vclstaff/.ssh/id_rsa.
 Your public key has been saved in /home/vclstaff/.ssh/id_rsa.pub.
 The key fingerprint is:
</code></pre></div>    </div>
  </li>
</ol>

<p>At this point we have created a private key /home/vclstaff/.ssh/id_rsa and the public key /home/vclstaff/.ssh/id_rsa.pub.</p>

<p>Copy the public key to /home/vclstaff/.ssh/authorized_keys file</p>

<div class="highlighter-rouge"><div class="highlight"><pre class="highlight"><code>    cat /home/vclstaff/.ssh/id_rsa.pub &gt; /home/vclstaff/.ssh/authorized_keys
</code></pre></div></div>

<p>Copy the private key to the management node. This can be stored in /etc/vcl/lab.key. This private key is used by vcld to remotely log into the the lab machine.</p>

<div class="highlighter-rouge"><div class="highlight"><pre class="highlight"><code>    Edit /etc/vcld.conf
    Set the variables IDENTITY_linux_lab and IDENTITY_solaris_lab to use this new key.
    It should look like:
    IDENTITY_solaris_lab=/etc/vcl/lab.key
    IDENTITY_linux_lab=/etc/vcl/lab.key
</code></pre></div></div>

<p>Test out the newly created key from the vcl management node:</p>

<div class="highlighter-rouge"><div class="highlight"><pre class="highlight"><code>    ssh -i /etc/vcl/lab.key vclstaff@target_lab_machine
</code></pre></div></div>

<ol>
  <li>
    <p>Set ssh server on target machine to listen on port 24. Edit /etc/ssh/sshd_config on target lab machine(s).</p>

    <div class="highlighter-rouge"><div class="highlight"><pre class="highlight"><code> echo "Port 24" &gt;&gt; /etc/ssh/sshd_config
</code></pre></div>    </div>
  </li>
</ol>

<p>For advanced ssh configurations one may need to also add vclstaff to the AllowUsers directive or some other group which would work with ones existing campus ssh login restrictions, if any.</p>

<div class="highlighter-rouge"><div class="highlight"><pre class="highlight"><code>    Restart sshd: /etc/init.d/sshd restart
</code></pre></div></div>

<p>Retest to make sure sshd is accessible on port 24</p>

<div class="highlighter-rouge"><div class="highlight"><pre class="highlight"><code>    ssh -p24 -i /etc/vcl/lab.key vclstaff@target_lab_machine
</code></pre></div></div>


      
      <div class="footer border-top border-gray-light mt-5 pt-3 text-right text-gray">
        This site is open source. <a href="http://github.com/pathoma4/vcl/edit/gh-pages/HowToProvisionLinuxandUnixLabComputers.md">Improve this page</a>.
      </div>
      
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/anchor-js/4.1.0/anchor.min.js" integrity="sha256-lZaRhKri35AyJSypXXs4o6OPFTbTmUoltBbDCbdzegg=" crossorigin="anonymous"></script>
    <script>anchors.add();</script>
    
  </body>
</html>
